<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RagController extends Controller
{
    public function ask(Request $request)
    {
        $question = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ])['question'];

        // Allow this request to run longer than PHP's default (workaround).
        // Prefer a background job or preloaded model in production.
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');
        // 1) Embed the question (1024 dims)
        try {
            $embedResp = Http::retry(3, 1000)->timeout(120)->post('http://localhost:11434/api/embed', [
                'model' => 'mxbai-embed-large',
                'input' => $question,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Embedding service error (exception)',
                'details' => $e->getMessage(),
            ], 502);
        }

        if (!$embedResp->ok()) {
            return response()->json([
                'error' => 'Embedding service failed',
                'details' => $embedResp->body(),
            ], 502);
        }

        $embed = $embedResp->json();
        $qvec = $embed['embeddings'][0] ?? null;

        if (!$qvec || !is_array($qvec)) {
            return response()->json(['error' => 'Embedding failed'], 500);
        }

        $qvecSql = '[' . implode(',', $qvec) . ']';

        // 2) Retrieve top chunks + distance (cosine distance)
        // Smaller distance = more similar
        $k = 4;

        $rows = DB::table('rag_chunks')
            ->select([
                'id',
                'source',
                'chunk_index',
                'content',
            ])
            ->selectRaw("embedding <=> ?::vector AS distance", [$qvecSql])
            ->orderByRaw("embedding <=> ?::vector", [$qvecSql])
            ->limit($k)
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'answer' => "I don't have any documents loaded yet.",
                'context_used' => 0,
                'sources' => [],
            ]);
        }

        // OPTIONAL: similarity gate (tune this)
        // Typical cosine distance ranges: 0 (identical) to 2 (opposite).
        // For embeddings, good matches are often < 0.35–0.6 depending on model/data.
        $best = $rows->first();
        $maxDistance = 0.65; // start here, tune later

        if ($best->distance !== null && $best->distance > $maxDistance) {
            return response()->json([
                'answer' => "I can’t find anything relevant in your documents to answer that.",
                'context_used' => 0,
                'sources' => $rows->map(fn($r) => [
                    'id' => $r->id,
                    'source' => $r->source,
                    'chunk_index' => $r->chunk_index,
                    'distance' => $r->distance,
                    'preview' => mb_substr($r->content, 0, 140) . '...',
                ])->values(),
            ]);
        }

        $context = $rows->pluck('content')->implode("\n\n---\n\n");

        // 3) Generate answer with strict grounding
        $prompt = <<<PROMPT
You are an assistant that answers ONLY using the CONTEXT.
If the answer is not explicitly in the CONTEXT, say: "I don't know based on the provided documents."

CONTEXT:
{$context}

QUESTION:
{$question}

Return a short, direct answer.
PROMPT;

        try {
            $genResp = Http::retry(3, 2000)->timeout(300)->post('http://localhost:11434/api/generate', [
                'model' => 'gemma3:4b',
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.1,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'LLM generation error (exception)',
                'details' => $e->getMessage(),
            ], 502);
        }

        if (!$genResp->ok()) {
            return response()->json([
                'error' => 'LLM generation failed',
                'details' => $genResp->body(),
            ], 502);
        }

        $gen = $genResp->json();

        return response()->json([
            'answer' => trim($gen['response'] ?? ''),
            'context_used' => $rows->count(),
            'sources' => $rows->map(fn($r) => [
                'id' => $r->id,
                'source' => $r->source,
                'chunk_index' => $r->chunk_index,
                'distance' => $r->distance,
                'preview' => mb_substr($r->content, 0, 140) . '...',
            ])->values(),
        ]);
    }
}
