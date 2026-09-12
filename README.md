# Integrated Employee Management System + Local RAG Assistant

This project combines a Laravel-based employee-management application with a **local retrieval-augmented generation (RAG) extension** for answering questions from internal policy documents.

The employee-management application came first. The RAG functionality was added later as an independent learning and engineering extension after professional exposure to AI-assisted document search. It should therefore be read as a separate technical progression rather than as work completed during the original internship.

## RAG workflow

The current implementation follows a straightforward, inspectable retrieval pipeline:

```text
PDF policy document
      ↓
Text extraction
      ↓
~200-word chunks
      ↓
Ollama embedding model
(mxbai-embed-large)
      ↓
PostgreSQL + pgvector
      ↓
Question embedding
      ↓
Top-k cosine-distance retrieval
      ↓
Similarity threshold
      ↓
Strictly grounded prompt
      ↓
Local Gemma generation
      ↓
Answer + retrieved sources
```

### Document ingestion

The `rag:ingest` command:

- parses a PDF with `Smalot\PdfParser`
- normalizes whitespace
- splits the text into approximately 200-word chunks
- creates embeddings through the local Ollama `/api/embed` endpoint using `mxbai-embed-large`
- stores each chunk, source name, chunk index, and vector in PostgreSQL/pgvector

### Retrieval

For each question, the controller:

- creates a 1024-dimensional query embedding
- retrieves the four closest chunks using pgvector cosine distance (`<=>`)
- applies a configurable similarity-distance gate
- refuses to generate a document-grounded answer when the best retrieved match is too weak

The current threshold is an engineering starting point and is explicitly marked in the source as something to tune rather than as a scientifically validated optimum.

### Grounded answer generation

The generation prompt instructs the model to answer **only from retrieved context** and to say that it does not know when the answer is absent from the documents. Generation currently uses a local `gemma3:4b` model through Ollama with a low temperature.

The API response includes source metadata, chunk indices, retrieval distances, and previews so the retrieved evidence can be inspected alongside the answer.

## Why I built this extension

My earlier software work had exposed me to AI-assisted semantic search over company policy material. After that experience, I wanted to understand what was happening beyond the chat interface, so I independently implemented the retrieval pipeline itself: parsing, chunking, embeddings, vector storage, nearest-neighbor retrieval, relevance gating, context construction, and constrained generation.

That progression is what made information retrieval and grounded language-model systems a serious technical interest rather than simply another API integration.

## Employee-management application

The underlying application is a Laravel/PHP employee-management system with conventional web-application concerns such as:

- employee and organizational records
- authentication and authorization
- role-based application behavior
- PostgreSQL-backed persistence
- modular Laravel application structure

The RAG layer complements those conventional application functions; it does not replace them.

## Technology

- PHP / Laravel
- PostgreSQL
- pgvector
- Ollama
- `mxbai-embed-large` embeddings
- Gemma local generation
- PDF text extraction with `smalot/pdfparser`

## Key source files

```text
app/Console/Commands/RagIngest.php
app/Http/Controllers/RagController.php
```

The implementation is intentionally compact so the complete retrieval path can be inspected without a large framework hiding the core logic.

## Limitations and next steps

This is an engineering prototype rather than a published RAG benchmark. Useful next steps would include:

- systematic evaluation of chunk size and overlap
- retrieval precision/recall evaluation on a labeled question set
- reranking experiments
- calibrated similarity thresholds
- citation-to-sentence verification
- comparisons across embedding models
- tests for adversarial or conflicting documents

Those are also the kinds of questions I want to approach more rigorously in graduate study.
