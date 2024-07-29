<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class MovieApiUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Successfull creates a movie
     */
    public function test_update_success(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create(['user_id' => $user->id]);
        $movie->categories()->sync(1, 2);
        Log::info("PRE UPDATE: " . json_encode($movie));

        $movie_updated_data = Movie::factory()->make(['user_id' => $user->id]);

        $this->actingAs($user, 'web');

        $payload = $movie_updated_data->toArray();
        $payload['categories'] = [2, 4]; //first 2 categories
        $response = $this->json('PUT', "/movies/{$movie->id}", $payload);

        // Assert the response
        Log::info("POST UPDATE: " . json_encode($response));
        $response->assertStatus(201);
    }

    public function test_update_failed_other_user(): void
    {
        $user2 = User::factory()->create();
        $user1 = User::factory()->create();
        $movie = Movie::factory()->create(['user_id' => $user1->id]);
        $movie->categories()->sync(1, 2);
        Log::info("PRE UPDATE: " . json_encode($movie));

        $movie_updated_data = Movie::factory()->make(['user_id' => $user1->id]);

        $this->actingAs($user2, 'web');

        $payload = $movie_updated_data->toArray();
        $payload['categories'] = [2, 4]; //first 2 categories
        $response = $this->json('PUT', "/movies/{$movie->id}", $payload);

        // Assert the response
        Log::info("POST UPDATE: " . json_encode($response));
        $response->assertStatus(403);
    }

}
