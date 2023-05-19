<?php

namespace Database\Seeders;

use App\Models\ProductDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductDetail::create([
            'product_id' => 1,
            'variant_id' => 1,
        ]);

        ProductDetail::create([
            'product_id' => 1,
            'variant_id' => 2,
        ]);

        ProductDetail::create([
            'product_id' => 1,
            'variant_id' => 3,
        ]);

        ProductDetail::create([
            'product_id' => 2,
            'variant_id' => 1,
        ]);

        ProductDetail::create([
            'product_id' => 2,
            'variant_id' => 2,
        ]);
    }
}
