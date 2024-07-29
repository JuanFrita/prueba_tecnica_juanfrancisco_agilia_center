<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyMovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Repositories\Movies\MovieRepositoryInterface;
use Request;

class MovieController extends Controller
{

    public function __construct(protected readonly MovieRepositoryInterface $movieRepositoryInterface)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('movies.index');
    }

    /**
     * Stores a new movie
     * @param \App\Http\Requests\StoreMovieRequest $request
     * @return mixed|MovieResource|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreMovieRequest $request)
    {
        $cleanData = $request->validated();
        $movie = $this->movieRepositoryInterface->createMovie($cleanData);
        $expectsJson = $request->expectsJson();
        return $this->movieResponse($movie, "Movie successfully created", "Movie can't be created", $expectsJson);
    }


    /**
     * Updates a movie
     * @param \App\Http\Requests\UpdateMovieRequest $request
     * @param \App\Models\Movie $movie
     * @return mixed|MovieResource|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        $cleanData = $request->validated();
        $movie = $this->movieRepositoryInterface->updateMovie($movie, $cleanData);
        $expectsJson = $request->expectsJson();
        return $this->movieResponse($movie, "Movie successfully updated", "Movie can't be updated", $expectsJson);
    }

    /**
     * 
     * @param mixed $movie
     * @param string $success_message
     * @param string $failed_message
     * @return mixed|MovieResource|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function movieResponse(?Movie $movie, string $success_message, string $failed_message, bool $expectsJson)
    {
        if (is_null($movie)) {
            return $expectsJson
                ? response()->json(['message' => $failed_message], 500)
                : redirect()->route('movies.index')->withErrors([$failed_message]);
        } else {
            return $expectsJson
                ? new MovieResource($movie)
                : redirect()->route('movies.index')->with('success', $success_message);
        }
    }

    /**
     * Destroys a movie
     * @param \Request $request
     * @param \App\Models\Movie $movie
     * @return mixed|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(DestroyMovieRequest $request, Movie $movie)
    {
        $result = $this->movieRepositoryInterface->softDestroyMovie($movie);
        $expectsJson = $request->expectsJson();
        $message = $result ? 'Movie successfully eliminated' : "Movie can't be eliminated";
        $status = $result ? 200 : 500;
        return $expectsJson ? response()->json(['message' => $message], $status) :
            redirect()->route('movies.index')->with($result ? 'success' : 'error', $message);
    }
}
