<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_research_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();
            $table->string('title', 300);
            $table->string('authors', 500)->nullable();
            $table->smallInteger('publication_year')->nullable();
            $table->string('journal', 200)->nullable();
            $table->string('pubmed_id', 20)->nullable();
            $table->string('url', 500);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('product_id');
            $table->index(['product_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_research_links');
    }
};
