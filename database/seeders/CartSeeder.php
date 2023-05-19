<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Sales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cart::create([
            'product_detail_id' => 1,
            'sales_id' => Sales::generateID(),
        ]);

        Cart::create([
            'product_detail_id' => 2,
            'sales_id' => Sales::generateID(),
        ]);
    }
}
