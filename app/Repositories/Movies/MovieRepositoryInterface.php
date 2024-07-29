<?php

namespace App\Repositories\Movies;

use App\Models\Movie;
use Illuminate\Pagination\LengthAwarePaginator;


interface MovieRepositoryInterface
{
    public function getMoviesByUserId(int $id, int $pagination = 10): LengthAwarePaginator;

    public function getMovieById(int $id): Movie;

    public function createMovie(array $data): ?Movie;

    public function updateMovie(Movie $movie, array $data): ?Movie;

    public function softDestroyMovie(Movie $movie): bool;

    public function forceDestroyMovie(Movie $movie): bool;

    public function isMovieOwnedByUser(int $movieId, int $userId): bool;
}
