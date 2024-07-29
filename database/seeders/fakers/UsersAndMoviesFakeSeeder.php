<?php

namespace Database\Seeders\Fakers;

use App\Models\Category;
use App\Models\Movie;
use App\Models\User;
use Hash;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class UsersAndMoviesFakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $password = Hash::make('1234');
        User::factory()->count(5)->state(
            new Sequence(
                ['email' => 'fake1@fakemail.com'],
                ['email' => 'fake2@fakemail.com'],
                ['email' => 'fake3@fakemail.com'],
                ['email' => 'fake4@fakemail.com'],
                ['email' => 'fake5@fakemail.com'],
            )
        )->create(['password' => $password])->each(function ($user) use ($categories) {
            Movie::factory(10)->create(['user_id' => $user->id])->each(function ($movie) use ($categories) {
                $movie->categories()->attach(
                    $categories->random(2)->pluck('id')->toArray()
                );
            });
        });
    }
}
