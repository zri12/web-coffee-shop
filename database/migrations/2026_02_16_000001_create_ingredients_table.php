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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category'); // e.g., Dairy, Coffee, Bakery, Sweetener
            $table->enum('unit', ['ml', 'gram', 'pcs'])->default('gram');
            $table->decimal('stock', 10, 2)->default(0); // Current stock amount
            $table->decimal('minimum_stock', 10, 2)->default(0); // Alert threshold
            $table->enum('status', ['Aman', 'Hampir Habis', 'Habis'])->default('Aman');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
