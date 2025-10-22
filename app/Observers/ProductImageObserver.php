<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProductImage;

class ProductImageObserver
{
    /**
     * Handle the ProductImage "creating" event.
     * This runs before a new image is created.
     */
    public function creating(ProductImage $image): void
    {
        // Auto-set sort_order to be last if not provided
        if ($image->sort_order === 0 || $image->sort_order === null) {
            $maxOrder = ProductImage::where('product_id', $image->product_id)->max('sort_order');
            $image->sort_order = $maxOrder ? $maxOrder + 1 : 0;
        }
    }

    /**
     * Handle the ProductImage "created" event.
     */
    public function created(ProductImage $image): void
    {
        // If this is marked as primary, unmark all other images for this product
        if ($image->is_primary) {
            ProductImage::where('product_id', $image->product_id)
                ->where('id', '!=', $image->id)
                ->update(['is_primary' => false]);
        }

        // If this is the first image for the product, make it primary automatically
        $imageCount = ProductImage::where('product_id', $image->product_id)->count();
        if ($imageCount === 1) {
            $image->is_primary = true;
            $image->saveQuietly(); // Save without triggering observer again
        }
    }

    /**
     * Handle the ProductImage "updating" event.
     */
    public function updating(ProductImage $image): void
    {
        // If this image is being marked as primary, unmark all others
        if ($image->isDirty('is_primary') && $image->is_primary) {
            ProductImage::where('product_id', $image->product_id)
                ->where('id', '!=', $image->id)
                ->update(['is_primary' => false]);
        }
    }

    /**
     * Handle the ProductImage "deleting" event.
     */
    public function deleting(ProductImage $image): void
    {
        // Delete the physical file from storage
        \Illuminate\Support\Facades\Storage::delete($image->path);

        // If we're deleting the primary image, make another image primary
        if ($image->is_primary) {
            $nextImage = ProductImage::where('product_id', $image->product_id)
                ->where('id', '!=', $image->id)
                ->orderBy('sort_order')
                ->first();

            if ($nextImage) {
                $nextImage->is_primary = true;
                $nextImage->saveQuietly();
            }
        }
    }
}
