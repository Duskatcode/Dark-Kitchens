<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Entradas', 'Platos fuertes', 'Bebidas', 'Postres'] as $category) {
            Category::query()->firstOrCreate(['name' => $category]);
        }
    }
}
