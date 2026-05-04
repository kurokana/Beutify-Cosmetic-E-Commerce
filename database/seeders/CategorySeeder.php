<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed sample product categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Lipstik',
                'description' => 'Produk pewarna bibir termasuk lipstik, lip gloss, lip tint, dan lip liner.',
            ],
            [
                'name'        => 'Skincare',
                'description' => 'Produk perawatan kulit termasuk serum, moisturizer, toner, dan sunscreen.',
            ],
            [
                'name'        => 'Foundation',
                'description' => 'Produk alas bedak untuk meratakan warna kulit dan menutupi noda.',
            ],
            [
                'name'        => 'Maskara',
                'description' => 'Produk untuk mempertebal, memanjangkan, dan melentikkan bulu mata.',
            ],
            [
                'name'        => 'Eyeshadow',
                'description' => 'Produk pewarna kelopak mata dalam berbagai warna dan finish.',
            ],
            [
                'name'        => 'Blush On',
                'description' => 'Produk perona pipi untuk memberikan warna alami pada wajah.',
            ],
            [
                'name'        => 'Parfum',
                'description' => 'Wewangian tubuh dalam berbagai varian aroma.',
            ],
            [
                'name'        => 'Perawatan Rambut',
                'description' => 'Produk perawatan rambut termasuk sampo, kondisioner, dan hair mask.',
            ],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['name' => $data['name']],
                [
                    'name'        => $data['name'],
                    'slug'        => Str::slug($data['name']),
                    'description' => $data['description'],
                ]
            );
        }

        $this->command->info(count($categories) . ' categories seeded.');
    }
}
