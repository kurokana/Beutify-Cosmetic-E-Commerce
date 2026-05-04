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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('sku')->unique();
            $table->unsignedInteger('weight')->comment('Weight in grams');
            $table->boolean('is_active')->default(true);
            $table->decimal('average_rating', 3, 1)->default(0);
            $table->timestamps();

            // Indexes
            $table->index('name', 'idx_products_name');
            $table->index(['is_active', 'category_id', 'brand_id'], 'idx_products_active');
            $table->index('price', 'idx_products_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
