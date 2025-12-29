<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Writing Tips',
            'Personal Essays',
            'Publishing',
            'Book Reviews',
            'Travel Notes',
            'Productivity',
        ];

        foreach ($categories as $order => $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Articles curated under {$name}.",
                    'order_column' => $order,
                ]
            );
        }
    }
}

