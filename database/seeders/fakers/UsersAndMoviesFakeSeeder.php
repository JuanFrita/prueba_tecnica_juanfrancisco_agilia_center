<?php

namespace Database\Seeders\Fakers;

use App\Models\Category;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersAndMoviesFakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        User::factory()->count(5)->create()->each(function ($user) use ($categories) {
            Movie::factory(10)->create(['user_id' => $user->id])->each(function ($movie) use ($categories) {
                $movie->categories()->attach(
                    $categories->random(2)->pluck('id')->toArray()
                );
            });
        });
    }
}
