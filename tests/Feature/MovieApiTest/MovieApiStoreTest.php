<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class MovieApiStoreTest extends TestCase
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
    public function test_store_success(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->make(['user_id' => $user->id]);

        $this->actingAs($user, 'web');

        $payload = $movie->toArray();
        $payload['categories'] = [1, 2]; //first 2 categories
        $response = $this->json('POST', '/movies', $payload);

        // Assert the response
        Log::info(json_encode($response));
        $response->assertStatus(201);
    }

    /**
     * 0 Categories indicated, should fail
     */
    public function test_store_failed_0_categories(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->make(['user_id' => $user->id]);

        $this->actingAs($user, 'web');

        $payload = $movie->toArray();
        $payload['categories'] = []; //first 2 categories
        $response = $this->json('POST', '/movies', $payload);

        // Assert the response
        Log::info(json_encode($response));
        $response->assertStatus(422);
    }

    /**
     * More than 4 categories indicated, should fail
     */
    public function test_store_failed_5_categories(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->make(['user_id' => $user->id]);

        $this->actingAs($user, 'web');

        $payload = $movie->toArray();
        $payload['categories'] = [1, 2, 3, 4, 5]; //first 2 categories
        $response = $this->json('POST', '/movies', $payload);

        // Assert the response
        Log::info(json_encode($response));
        $response->assertStatus(422);
    }

    /**
     * Tries to updated a movie for another user_id, should fail
     */
    public function test_store_another_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $movie = Movie::factory()->make(['user_id' => $user2->id]);

        $this->actingAs($user1, 'web');

        $payload = $movie->toArray();
        $payload['categories'] = [1, 2, 3, 4, 5]; //first 2 categories
        $response = $this->json('POST', '/movies', $payload);

        // Assert the response
        Log::info(json_encode($response));
        $response->assertStatus(403);
    }

}
