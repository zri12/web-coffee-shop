<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredient_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->decimal('change_amount', 10, 2); // Positive for restock, negative for deduction
            $table->enum('type', ['Order Deduct', 'Restock']);
            $table->unsignedBigInteger('reference_id')->nullable(); // Order ID or Restock ID
            $table->text('note')->nullable(); // Additional notes (supplier, reason, etc.)
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for performance
            $table->index('ingredient_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_logs');
    }
};
