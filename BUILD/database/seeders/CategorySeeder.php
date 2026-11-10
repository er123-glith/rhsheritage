<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Songkok',
                'slug' => 'songkok',
                'sort_order' => 1,
            ],
            [
                'name' => 'Sarung',
                'slug' => 'sarung',
                'sort_order' => 2,
            ],
            [
                'name' => 'Jilbab/Kerudung',
                'slug' => 'jilbab',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
