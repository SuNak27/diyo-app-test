<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@mail.com',
            'password' => Hash::make('12345678'),
        ]);

        $this->call([
            InventorySeeder::class,
            VariantSeeder::class,
            ProductSeeder::class,
            ProductDetailSeeder::class,
            SalesSeeder::class,
            CartSeeder::class,
        ]);
    }
}
