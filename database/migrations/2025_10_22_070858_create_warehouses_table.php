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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();

            // Basic fields
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('address')->nullable();

            // Location (latitude/longitude stored as JSON)
            $table->json('location')->nullable();

            // Working hours
            $table->text('working_hours')->nullable();

            // Contact
            $table->string('phone')->nullable();
            $table->string('url')->nullable();

            // Flags
            $table->boolean('is_physical_store')->default(false);
            $table->boolean('active')->default(true);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('active');
            $table->index('is_physical_store');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
