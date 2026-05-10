<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed skincare products.
     */
    public function run(): void
    {
        $skincareCategory = Category::where('name', 'Skincare')->first();
        
        if (!$skincareCategory) {
            $this->command->error('Skincare category not found. Please run CategorySeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'Wardah Hydrating Toner',
                'description' => 'Toner pelembab dengan kandungan hyaluronic acid yang membantu menjaga kelembaban kulit.',
                'price' => 89000,
                'stock' => 50,
                'sku' => 'WARD-HYDRA-TONER-001',
                'weight' => 100,
                'brand_name' => 'Wardah',
            ],
            [
                'name' => 'Emina Sea Bright Serum',
                'description' => 'Serum terang dengan vitamin C yang membantu mencerahkan dan meremajakan kulit.',
                'price' => 65000,
                'stock' => 40,
                'sku' => 'EMIN-BRIGHT-SERUM-001',
                'weight' => 50,
                'brand_name' => 'Emina',
            ],
            [
                'name' => 'Pixy UV Essentials Daily Sunscreen SPF 32',
                'description' => 'Sunscreen ringan yang melindungi kulit dari sinar UV dan cocok untuk penggunaan sehari-hari.',
                'price' => 45000,
                'stock' => 60,
                'sku' => 'PIXY-UV-ESSENTIALS-001',
                'weight' => 60,
                'brand_name' => 'Pixy',
            ],
            [
                'name' => 'Implora Pearl Serum Moisturizer',
                'description' => 'Serum pelembab dengan ekstrak mutiara yang memberikan nutrisi dan kelembaban maksimal.',
                'price' => 55000,
                'stock' => 45,
                'sku' => 'IMPL-PEARL-SERUM-001',
                'weight' => 50,
                'brand_name' => 'Implora',
            ],
            [
                'name' => "L'Oréal Paris Revitalift Anti-Aging Moisturizer",
                'description' => 'Krim anti-aging dengan teknologi revitalift untuk mengurangi garis halus dan kerutan.',
                'price' => 165000,
                'stock' => 35,
                'sku' => 'LOREAL-REVITA-CREAM-001',
                'weight' => 80,
                'brand_name' => "L'Oréal",
            ],
            [
                'name' => 'Wardah Pure Brightening Mask',
                'description' => 'Masker wajah dengan kandungan ekstrak mawar dan vitamin E untuk kulit yang lebih cerah.',
                'price' => 78000,
                'stock' => 55,
                'sku' => 'WARD-BRIGHT-MASK-001',
                'weight' => 100,
                'brand_name' => 'Wardah',
            ],
        ];

        foreach ($products as $productData) {
            $brand = Brand::where('name', $productData['brand_name'])->first();
            
            if (!$brand) {
                $this->command->warn("Brand '{$productData['brand_name']}' not found. Skipping product '{$productData['name']}'");
                continue;
            }

            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                [
                    'brand_id' => $brand->id,
                    'category_id' => $skincareCategory->id,
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'sku' => $productData['sku'],
                    'weight' => $productData['weight'],
                    'is_active' => true,
                    'average_rating' => 0,
                ]
            );

            $this->command->info("Product '{$productData['name']}' created successfully.");
        }

        $this->command->info('6 skincare products seeded.');
    }
}
