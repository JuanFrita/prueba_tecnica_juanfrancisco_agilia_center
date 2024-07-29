<?php

namespace Database\Seeders;

use Database\Seeders\Fakers\UsersAndMoviesFakeSeeder;
use Illuminate\Database\Seeder;

class DatabaseFakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UsersAndMoviesFakeSeeder::class
        ]);
    }
}
