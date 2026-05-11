<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category' => 'Entradas',
                'name' => 'Papas rústicas',
                'description' => 'Papas doradas con especias de la casa y salsa cremosa.',
                'price' => 12000,
                'is_available' => true,
            ],
            [
                'category' => 'Platos fuertes',
                'name' => 'Burger clásica',
                'description' => 'Hamburguesa artesanal con carne, queso, vegetales frescos y salsa de la casa.',
                'price' => 24000,
                'is_available' => true,
            ],
            [
                'category' => 'Platos fuertes',
                'name' => 'Bowl de pollo',
                'description' => 'Bowl con pollo, arroz, vegetales salteados y salsa especial.',
                'price' => 22000,
                'is_available' => true,
            ],
            [
                'category' => 'Bebidas',
                'name' => 'Limonada natural',
                'description' => 'Bebida fría preparada con limón fresco.',
                'price' => 7000,
                'is_available' => true,
            ],
            [
                'category' => 'Postres',
                'name' => 'Brownie de chocolate',
                'description' => 'Brownie húmedo de chocolate con textura suave.',
                'price' => 9000,
                'is_available' => true,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::query()->firstOrCreate([
                'name' => $productData['category'],
            ]);

            Product::query()->updateOrCreate(
                ['name' => $productData['name']],
                [
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'is_available' => $productData['is_available'],
                    'category_id' => $category->id,
                ]
            );
        }
    }
}
