<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository
{
    /**
     * Retrieve a paginated list of active products with optional filters and sorting.
     *
     * Supports the following filter keys:
     *   - category_id  (int|string)  Filter by category  [Req 2.2]
     *   - brand_id     (int|string)  Filter by brand     [Req 2.3]
     *   - keyword      (string)      Full-text search    [Req 2.4]
     *   - min_price    (float)       Minimum price       [Req 2.5]
     *   - max_price    (float)       Maximum price       [Req 2.5]
     *
     * Supported sort values: latest, price_asc, price_desc, rating_desc [Req 2.6]
     * Pagination: 24 products per page by default                        [Req 2.7]
     *
     * @param  array<string, mixed>  $filters
     * @param  string                $sort     latest|price_asc|price_desc|rating_desc
     * @param  int                   $perPage
     * @return LengthAwarePaginator
     */
    public function getFiltered(
        array $filters = [],
        string $sort = 'latest',
        int $perPage = 24
    ): LengthAwarePaginator {
        $query = Product::query()
            ->active()
            ->with(['brand', 'category', 'images']);

        // ── Filters ──────────────────────────────────────────────────────────

        if (!empty($filters['category_id'])) {
            $query->filterByCategory($filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->filterByBrand($filters['brand_id']);
        }

        if (!empty($filters['keyword'])) {
            $query->searchByKeyword($filters['keyword']);
        }

        $minPrice = isset($filters['min_price']) && $filters['min_price'] !== '' ? (float) $filters['min_price'] : null;
        $maxPrice = isset($filters['max_price']) && $filters['max_price'] !== '' ? (float) $filters['max_price'] : null;

        if ($minPrice !== null || $maxPrice !== null) {
            $query->filterByPrice($minPrice, $maxPrice);
        }

        // ── Sorting ───────────────────────────────────────────────────────────

        match ($sort) {
            'price_asc'   => $query->orderBy('price', 'asc'),
            'price_desc'  => $query->orderBy('price', 'desc'),
            'rating_desc' => $query->orderBy('average_rating', 'desc'),
            default       => $query->orderBy('created_at', 'desc'), // 'latest'
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active categories, cached for 60 minutes. [Req 14.7]
     *
     * @return Collection<int, Category>
     */
    public function getAllCategories(): Collection
    {
        return Cache::remember('categories_all', 60 * 60, function () {
            return Category::orderBy('name')->get();
        });
    }

    /**
     * Get all active brands, cached for 60 minutes. [Req 14.7]
     *
     * @return Collection<int, Brand>
     */
    public function getAllBrands(): Collection
    {
        return Cache::remember('brands_all_active', 60 * 60, function () {
            return Brand::where('is_active', true)->orderBy('name')->get();
        });
    }

    /**
     * Get related products from the same category, excluding the given product. [Req 2.10]
     *
     * @param  Product  $product
     * @param  int      $limit
     * @return Collection<int, Product>
     */
    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return Product::query()
            ->active()
            ->filterByCategory($product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['brand', 'images'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get best-selling products based on order items count, cached for 60 minutes.
     * Requirements: 14.7 - Caching for frequently accessed data
     *
     * @param  int  $limit
     * @return Collection<int, Product>
     */
    public function getBestSellers(int $limit = 8): Collection
    {
        return Cache::remember('best_sellers', 60 * 60, function () use ($limit) {
            return Product::query()
                ->active()
                ->with(['brand', 'images'])
                ->withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get latest products, cached for 30 minutes.
     * Requirements: 14.7 - Caching for frequently accessed data
     *
     * @param  int  $limit
     * @return Collection<int, Product>
     */
    public function getLatestProducts(int $limit = 8): Collection
    {
        return Cache::remember('latest_products', 30 * 60, function () use ($limit) {
            return Product::query()
                ->active()
                ->with(['brand', 'images'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured brands (active brands with logo), cached for 60 minutes.
     * Requirements: 14.7 - Caching for frequently accessed data
     *
     * @return Collection<int, Brand>
     */
    public function getFeaturedBrands(): Collection
    {
        return Cache::remember('featured_brands', 60 * 60, function () {
            return Brand::where('is_active', true)
                ->whereNotNull('logo_path')
                ->orderBy('name')
                ->get();
        });
    }
}
