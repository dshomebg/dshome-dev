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
        Schema::create('product_warehouse', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->onDelete('cascade');

            // Stock quantities
            $table->integer('quantity')->default(0);
            $table->integer('reserved')->default(0);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(['product_id', 'warehouse_id']);
            $table->index('product_id');
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse');
    }
};
