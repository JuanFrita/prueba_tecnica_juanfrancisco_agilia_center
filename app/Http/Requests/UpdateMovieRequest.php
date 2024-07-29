<?php

namespace App\Http\Requests;

use App\Repositories\Movies\MovieRepositoryInterface;

class UpdateMovieRequest extends StoreMovieRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $parent_authorize = parent::authorize();
        $movie = $this->route('movie');
        return $parent_authorize && app(MovieRepositoryInterface::class)->isMovieOwnedByUser($movie->id, $this->user()->id);
    }
}