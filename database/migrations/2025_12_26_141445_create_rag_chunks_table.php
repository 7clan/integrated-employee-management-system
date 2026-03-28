<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rag_chunks', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('source')->nullable(); // e.g. pdf filename
            $table->integer('chunk_index')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // OpenAI embeddings commonly 1024 dims (adjust later if needed)
        DB::statement("ALTER TABLE rag_chunks ADD COLUMN embedding vector(1024)");

        // Index (IVFFLAT). You can add later after you have data, but it's fine now.
        DB::statement("CREATE INDEX rag_chunks_embedding_idx ON rag_chunks USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)");
    }

    public function down(): void
    {
        Schema::dropIfExists('rag_chunks');
    }
};
