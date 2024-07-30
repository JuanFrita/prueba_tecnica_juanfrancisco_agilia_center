<?php

namespace App\Repositories\Movies\Eloquent;

use App\Models\Category;
use App\Models\Movie;
use App\Repositories\Movies\MovieRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MovieEloquentRepository implements MovieRepositoryInterface
{

    protected function query()
    {
        return Movie::query();
    }

    /**
     * Retrieve al movies from database related to a user
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMoviesByCriteria(Collection $criteriaCollection, int $pagination = 10): LengthAwarePaginator
    {
        $query = $this->query();

        $criteriaCollection->each(function ($criterion) use ($query) {
            $criterion->filter($query);
        });

        return $query->paginate($pagination);
    }

    /**
     * Retrive a movie by id
     * @param int $id Movie's id
     * @return ?Movie
     */
    public function getMovieById(int $id): Movie
    {
        return Movie::find($id);
    }

    /**
     * Get all movies categories
     * @return \Illuminate\Support\Collection
     */
    public function getCategories(): Collection
    {
        return Category::all();
    }


    /**
     * Creates a movie. If categories are specified they are synched
     * @param array $data Movie's data
     * @return ?Movie
     */
    public function createMovie(array $data): ?Movie
    {
        DB::beginTransaction();
        try {
            $movie = Movie::create($data);
            $movie->categories()->sync($data['categories']);
            DB::commit();
            return $movie;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error(__CLASS__ . " ::createMovie: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Updated a movie. If categories are specified they are synched
     * @param Movie $movie 
     * @param array $data Movie's updated data
     * @return ?Movie
     */
    public function updateMovie(Movie $movie, array $data): ?Movie
    {
        DB::beginTransaction();
        try {
            $movie->update($data);
            $movie->categories()->sync($data['categories']);
            DB::commit();
            return $movie;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error(__CLASS__ . " ::updateMovie: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Soft deletes a movie
     * @param \App\Models\Movie $movie
     * @return bool
     */
    public function softDestroyMovie(Movie $movie): bool
    {
        return $movie->delete();
    }

    /**
     * Force deletes a movie
     * @param \App\Models\Movie $movie
     * @return bool
     */
    public function forceDestroyMovie(Movie $movie): bool
    {
        return $movie->forceDelete();
    }

    /**
     * Checks if a movie is owned by a user
     * @param int $movieId
     * @param int $userId
     * @return bool
     */
    public function isMovieOwnedByUser(int $movieId, int $userId): bool
    {
        $movie = $this->getMovieById($movieId);
        return $movie?->user_id === $userId;
    }

}
