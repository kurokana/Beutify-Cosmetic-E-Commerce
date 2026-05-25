<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\User;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductImageObserver;
use App\Observers\ReviewObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers for admin activity logging and cache invalidation
        Product::observe(ProductObserver::class);
        ProductImage::observe(ProductImageObserver::class);
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
        Review::observe(ReviewObserver::class);
    }
}
