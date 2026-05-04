<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Seed sample cosmetic brands.
     */
    public function run(): void
    {
        $brands = [
            [
                'name'        => 'Wardah',
                'description' => 'Brand kosmetik halal Indonesia yang menawarkan produk berkualitas tinggi.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Maybelline',
                'description' => 'Brand kosmetik internasional dengan berbagai produk makeup inovatif.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Emina',
                'description' => 'Brand kosmetik lokal Indonesia yang menyasar segmen remaja dan dewasa muda.',
                'is_active'   => true,
            ],
            [
                'name'        => "L'Oréal",
                'description' => "Brand kosmetik global terkemuka dengan produk skincare dan makeup premium.",
                'is_active'   => true,
            ],
            [
                'name'        => 'Pixy',
                'description' => 'Brand kosmetik Indonesia yang menawarkan produk makeup terjangkau dan berkualitas.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Implora',
                'description' => 'Brand kosmetik lokal dengan harga terjangkau dan pilihan warna yang beragam.',
                'is_active'   => true,
            ],
        ];

        foreach ($brands as $data) {
            Brand::firstOrCreate(
                ['name' => $data['name']],
                [
                    'name'        => $data['name'],
                    'slug'        => Str::slug($data['name']),
                    'description' => $data['description'],
                    'is_active'   => $data['is_active'],
                ]
            );
        }

        $this->command->info(count($brands) . ' brands seeded.');
    }
}
