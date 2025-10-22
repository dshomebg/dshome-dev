<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "saving" event.
     * This runs before the product is saved (both create and update).
     */
    public function saving(Product $product): void
    {
        // Auto-generate slug from name if not provided
        if (empty($product->slug) && !empty($product->name)) {
            $product->slug = \Illuminate\Support\Str::slug($product->name);
        }

        // Auto-generate meta_title from name if not provided
        if (empty($product->meta_title) && !empty($product->name)) {
            $product->meta_title = $product->name;
        }

        // Auto-generate meta_description from description if not provided
        if (empty($product->meta_description) && !empty($product->description)) {
            $product->meta_description = \Illuminate\Support\Str::limit($product->description, 155);
        }
    }

    /**
     * Handle the Product "saved" event.
     * This runs after the product is saved.
     */
    public function saved(Product $product): void
    {
        // Auto-generate ALT text for images without one
        foreach ($product->images as $index => $image) {
            if (empty($image->alt_text)) {
                $image->alt_text = $product->name . ' - ' . ($index + 1);
                $image->save();
            }
        }

        // If no primary image is set, make the first image primary
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        if (!$hasPrimary && $product->images()->count() > 0) {
            $firstImage = $product->images()->orderBy('sort_order')->first();
            $firstImage->is_primary = true;
            $firstImage->save();
        }

        // Ensure only ONE image is marked as primary
        // If multiple images are marked as primary, keep only the first one
        $primaryImages = $product->images()->where('is_primary', true)->get();

        if ($primaryImages->count() > 1) {
            // Keep the first one, unmark the rest
            $primaryImages->skip(1)->each(function ($image) {
                $image->is_primary = false;
                $image->save();
            });
        }
    }

    /**
     * Handle the Product "deleting" event.
     */
    public function deleting(Product $product): void
    {
        // When a product is deleted (soft delete), we keep the images
        // If you want to delete images when product is deleted, uncomment below:

        // foreach ($product->images as $image) {
        //     // Delete physical file
        //     \Illuminate\Support\Facades\Storage::delete($image->path);
        //     // Delete database record
        //     $image->delete();
        // }
    }

    /**
     * Handle the Product "forceDeleted" event.
     * This runs when a product is permanently deleted.
     */
    public function forceDeleted(Product $product): void
    {
        // When a product is force deleted, delete all images
        foreach ($product->images as $image) {
            // Delete physical file
            \Illuminate\Support\Facades\Storage::delete($image->path);
            // Delete database record
            $image->forceDelete();
        }
    }
}
