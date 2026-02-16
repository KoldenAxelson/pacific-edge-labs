<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('sku', 30)->unique();
            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description');
            $table->string('form', 100)->nullable();
            $table->string('concentration', 100)->nullable();
            $table->string('storage_conditions', 300)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('slug');
            $table->index('active');
            $table->index('featured');
            $table->index(['active', 'deleted_at']);
        });

        // Full-text search index (PostgreSQL only)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX products_fulltext_idx ON products USING GIN (to_tsvector('english', name || ' ' || description))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
