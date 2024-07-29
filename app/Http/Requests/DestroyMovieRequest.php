<?php

namespace App\Http\Requests;

use App\Repositories\Movies\MovieRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class DestroyMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $movie = $this->route('movie');
        return app(MovieRepositoryInterface::class)->isMovieOwnedByUser($movie->id, $this->user()->id);
    }

}
