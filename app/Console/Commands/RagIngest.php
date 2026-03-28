<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;

class RagIngest extends Command
{
    protected $signature = 'rag:ingest {pdf}';
    protected $description = 'Ingest a PDF into RAG (chunk + embed + store)';

    public function handle()
    {
        $pdfName = $this->argument('pdf');
        
        $path = storage_path("app/rag/{$pdfName}");

        if (!file_exists($path)) {
            $this->error("PDF not found: {$path}");
            return;
        }

        $this->info("Parsing PDF...");
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = preg_replace('/\s+/', ' ', $pdf->getText());

        $chunks = array_chunk(explode(' ', $text), 200);
        $this->info("Total chunks: " . count($chunks));

        foreach ($chunks as $i => $chunkWords) {
            $content = implode(' ', $chunkWords);

            $resp = Http::timeout(60)->post(
                'http://localhost:11434/api/embed',
                [
                    'model' => 'mxbai-embed-large',
                    'input' => $content,
                ]
            )->json();

            $vec = $resp['embeddings'][0] ?? null;
            if (!$vec) {
                $this->warn("Skipping chunk {$i} (no embedding)");
                continue;
            }

            $vecSql = '[' . implode(',', $vec) . ']';

            DB::table('rag_chunks')->insert([
                'content' => $content,
                'source' => $pdfName,
                'chunk_index' => $i,
                'embedding' => DB::raw("'{$vecSql}'"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->line("Inserted chunk {$i}");
        }

        $this->info("Ingest complete");
    }
}
