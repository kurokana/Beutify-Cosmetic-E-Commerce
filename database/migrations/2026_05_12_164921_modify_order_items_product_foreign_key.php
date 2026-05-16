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
        Schema::table('order_items', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['product_id']);
            
            // Add new foreign key with cascadeOnDelete
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['product_id']);
            
            // Restore original foreign key with restrictOnDelete
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });
    }
};
