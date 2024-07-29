<?php

namespace Database\Seeders\Required;

use DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category_names = collect($this->data());
        $categories = $category_names->map(function ($category_name) {
            return [
                'name' => $category_name,
                'created_at' => now(),
                'updated_at' => now()
            ];
        });
        DB::table('categories')->insert($categories->toArray());
    }

    private function data(): array
    {
        return [
            'Terror',
            'Suspense',
            'Romántico',
            'Acción',
            'Fantasía',
            'Comedia',
            'Aventura',
            'Drama'
        ];

    }
}
