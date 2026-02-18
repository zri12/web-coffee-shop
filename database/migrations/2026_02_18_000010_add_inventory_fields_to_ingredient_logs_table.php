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
        Schema::table('ingredient_logs', function (Blueprint $table) {
            $table->enum('direction', ['IN', 'OUT'])->default('OUT')->after('type');
            $table->foreignId('order_id')->nullable()->after('reference_id')->constrained('orders')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->after('order_id')->constrained('menus')->nullOnDelete();
            $table->decimal('previous_stock', 10, 2)->nullable()->after('product_id');
            $table->decimal('new_stock', 10, 2)->nullable()->after('previous_stock');

            $table->index('order_id');
            $table->index('product_id');
            $table->index('direction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredient_logs', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['direction']);

            $table->dropColumn([
                'direction',
                'order_id',
                'product_id',
                'previous_stock',
                'new_stock',
            ]);
        });
    }
};
