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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();

            // Basic fields
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Status
            $table->boolean('active')->default(true);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
