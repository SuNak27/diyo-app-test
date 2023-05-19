<?php

namespace Database\Seeders;

use App\Models\Variant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Variant::create([
            'name' => 'Original',
            'additional_price' => 0,
        ]);

        Variant::create([
            'name' => 'Mushroom',
            'additional_price' => 10000,
        ]);

        Variant::create([
            'name' => 'Chicken',
            'additional_price' => 20000,
        ]);
    }
}
