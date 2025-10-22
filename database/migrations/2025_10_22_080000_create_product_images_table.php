<?php

declare(strict_types=1);

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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            // Product relationship
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Image file path (stored in storage/app/public/products)
            $table->string('path');

            // ALT text for SEO and accessibility
            $table->string('alt_text')->nullable();

            // Is this the primary/featured image?
            $table->boolean('is_primary')->default(false);

            // Sort order for displaying images
            $table->integer('sort_order')->default(0);

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('product_id');
            $table->index(['product_id', 'is_primary']);
            $table->index(['product_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
