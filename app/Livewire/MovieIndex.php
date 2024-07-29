<?php

namespace App\Livewire;

use App\Repositories\Movies\MovieRepositoryInterface;
use Livewire\Component;
use Livewire\WithPagination;

class MovieIndex extends Component
{
    use WithPagination;

    private MovieRepositoryInterface $movieRepositoryInterface;

    public function boot(
        MovieRepositoryInterface $movieRepositoryInterface
    ) {
        $this->movieRepositoryInterface = $movieRepositoryInterface;
    }

    public function render()
    {
        $movies = $this->movieRepositoryInterface->getMoviesByUserId(auth()->user()->id, 5);
        return view('livewire.movie-index', [
            'movies' => $movies
        ]);
    }
}
