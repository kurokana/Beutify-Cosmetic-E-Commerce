<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->logActivity('created', $product, null, $product->toArray());
        $this->invalidateProductCaches($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->logActivity('updated', $product, $product->getOriginal(), $product->getChanges());
        $this->invalidateProductCaches($product);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->logActivity('deleted', $product, $product->toArray(), null);
        $this->invalidateProductCaches($product);
    }

    /**
     * Log the activity to admin_logs table.
     */
    private function logActivity(string $action, Product $product, ?array $oldValues, ?array $newValues): void
    {
        // Only log if the user is authenticated and is an admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return;
        }

        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'model_type' => Product::class,
            'model_id' => $product->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Invalidate all product-related caches when a product is created, updated, or deleted.
     * Requirements: 14.7 - Cache invalidation on data changes
     */
    private function invalidateProductCaches(Product $product): void
    {
        // Invalidate homepage caches (latest products and best sellers)
        Cache::forget('latest_products');
        Cache::forget('best_sellers');

        // Invalidate product-specific rating cache
        Cache::forget("product_avg_rating_{$product->id}");

        // If product status changed, invalidate category/brand caches
        if ($product->wasChanged('is_active') || $product->wasChanged('category_id') || $product->wasChanged('brand_id')) {
            Cache::forget('categories_all');
            Cache::forget('brands_all_active');
        }
    }
}
