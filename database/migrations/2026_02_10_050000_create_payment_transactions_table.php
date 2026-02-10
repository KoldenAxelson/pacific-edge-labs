<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            // Revisit in Phase 4
            // $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Transaction details
            $table->string('transaction_id')->unique();
            $table->string('gateway')->default('mock'); // mock, authorize_net, square, etc.
            $table->enum('type', ['charge', 'refund', 'void'])->default('charge');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            
            // Amount
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Payment method (last 4 digits only for security)
            $table->string('payment_method')->nullable(); // e.g., "Visa ****1234"
            
            // Response data
            $table->text('gateway_response')->nullable(); // JSON encoded response
            $table->text('error_message')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['order_id', 'type']);
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
