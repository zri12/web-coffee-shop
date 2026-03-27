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
        if (!Schema::hasTable('ingredients')) {
            Schema::create('ingredients', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('category');
                $table->enum('unit', ['ml', 'gram', 'pcs'])->default('gram');
                $table->decimal('stock', 10, 2)->default(0);
                $table->decimal('minimum_stock', 10, 2)->default(0);
                $table->enum('status', ['Aman', 'Hampir Habis', 'Habis'])->default('Aman');
                $table->timestamps();
                $table->index('category');
                $table->index('status');
            });
        } else {
            Schema::table('ingredients', function (Blueprint $table) {
                if (!Schema::hasColumn('ingredients', 'category')) {
                    $table->string('category')->after('name');
                    $table->index('category');
                }
                
                // Add status index if missing
                try {
                    $table->index('status');
                } catch (\Exception $e) {}
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
