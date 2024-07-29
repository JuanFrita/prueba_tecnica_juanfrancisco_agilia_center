<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class MovieApiDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test soft deleting a movie
     */
    public function test_soft_delete(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'web');

        $response = $this->json("DELETE", "/movies/{$movie->id}");

        // Assert the response
        Log::info("POST UPDATE: " . json_encode($response));
        $response->assertStatus(200);
        $movie = Movie::withTrashed()->first();
        $this->assertNotEmpty($movie->deleted_at);
    }

}
