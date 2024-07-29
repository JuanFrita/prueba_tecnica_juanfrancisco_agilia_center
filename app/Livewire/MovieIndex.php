<?php

namespace App\Livewire;

use App\Filters\CategoryFilter;
use App\Filters\NameFilter;
use App\Filters\UserFilter;
use App\Repositories\Movies\MovieRepositoryInterface;
use Livewire\Component;
use Livewire\WithPagination;

class MovieIndex extends Component
{
    use WithPagination;

    private MovieRepositoryInterface $movieRepositoryInterface;

    public $name;
    public $categoryId;


    public function boot(
        MovieRepositoryInterface $movieRepositoryInterface
    ) {
        $this->movieRepositoryInterface = $movieRepositoryInterface;
    }
    
    public function updatedName()
    {
        $this->resetPage();
    }
    
    public function updatedCategoryId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $criteria = collect([
            new UserFilter(auth()->user()->id),
            ...($this->name ? [new NameFilter($this->name)] : []),
            ...($this->categoryId ? [new CategoryFilter($this->categoryId)] : [])
        ]);
        $movies = $this->movieRepositoryInterface->getMoviesByCriteria($criteria, 5);
        return view('livewire.movie-index', [
            'movies' => $movies,
            'categories' => $this->movieRepositoryInterface->getCategories()
        ]);
    }
}
