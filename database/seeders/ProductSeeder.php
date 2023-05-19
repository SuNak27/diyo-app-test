<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Spaghetti Aglio Olio',
            "description" => 'Spaghetti that cooked with onion and olive oil',
            'price' => 50000
        ]);

        Product::create([
            'name' => 'Meat Ball Spaghetti',
            "description" => 'Spaghetti that cooked with meat ball',
            'price' => 50000
        ]);
    }
}
