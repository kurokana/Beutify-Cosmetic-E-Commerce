<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ReviewService $reviewService,
    ) {}

    /**
     * Display the product catalog with filters, sorting, and pagination.
     * Requirements: 2.1, 2.2, 2.3, 2.5, 2.6, 2.7
     */
    public function index(Request $request): View
    {
        $filters = [
            'category_id' => $request->input('category_id'),
            'brand_id'    => $request->input('brand_id'),
            'min_price'   => $request->input('min_price'),
            'max_price'   => $request->input('max_price'),
        ];

        $sort = $request->input('sort', 'latest');

        $products   = $this->productRepository->getFiltered($filters, $sort);
        $categories = $this->productRepository->getAllCategories();
        $brands     = $this->productRepository->getAllBrands();

        return view('customer.catalog.index', compact('products', 'categories', 'brands', 'filters', 'sort'));
    }

    /**
     * Display the product detail page.
     * Requirements: 2.8, 2.9, 2.10
     */
    public function show(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'brand',
                'category',
                'images',
                'variants',
                'reviews.user',
            ])
            ->firstOrFail();

        $relatedProducts = $this->productRepository->getRelatedProducts($product, 4);

        // Review eligibility — Requirements 7.1, 7.3, 7.4
        $user           = auth()->user();
        $canReview      = false;
        $hasReviewed    = false;
        $eligibleOrder  = null;

        if ($user) {
            $eligibleOrder = $this->reviewService->getEligibleOrder($user, $product);
            $canReview     = $eligibleOrder !== null;

            if ($canReview) {
                $hasReviewed = $this->reviewService->hasReviewed($user, $product, $eligibleOrder->id);
            }
        }

        return view('customer.catalog.show', compact(
            'product',
            'relatedProducts',
            'canReview',
            'hasReviewed',
            'eligibleOrder',
        ));
    }

    /**
     * Display the search results page.
     * Requirements: 2.4, 13.5
     */
    public function search(Request $request): View
    {
        $keyword = trim($request->input('q', ''));

        $products   = collect();
        $categories = $this->productRepository->getAllCategories();
        $brands     = $this->productRepository->getAllBrands();

        if ($keyword !== '') {
            $filters = ['keyword' => $keyword];
            $sort    = $request->input('sort', 'latest');
            $products = $this->productRepository->getFiltered($filters, $sort);
        }

        return view('customer.catalog.search', compact('products', 'keyword', 'categories', 'brands'));
    }
}
