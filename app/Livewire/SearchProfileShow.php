<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Genre;
use App\Models\NewsOutlet;
use App\Models\SearchProfile;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

final class SearchProfileShow extends Component
{
    use WithPagination;

    public SearchProfile $searchProfile;

    public string $search = '';

    public array $selectedGenre = [];

    public array $selectedNewsOutlet = [];

    public function mount(SearchProfile $searchProfile): void
    {
        $this->authorize('view', $searchProfile);
        $this->searchProfile = $searchProfile;
    }

    #[Title('Search Profile Articles')]
    public function render(): View
    {
        $articles = $this->searchProfile->articles()
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->selectedGenre, function ($query): void {
                $query->whereHas('genres', function ($q): void {
                    $q->whereIn('genre_id', $this->selectedGenre);
                });
            })
            ->when($this->selectedNewsOutlet, function ($query): void {
                $query->whereIn('news_outlet_id', $this->selectedNewsOutlet);
            })
            ->latest('published_at')
            ->paginate(20);

        $genres = Genre::whereHas('articles', function ($query): void {
            $query->whereIn('articles.id', $this->searchProfile->articles()->pluck('articles.id'));
        })->get();

        $newsOutlets = NewsOutlet::whereHas('articles', function ($query): void {
            $query->whereIn('articles.id', $this->searchProfile->articles()->pluck('articles.id'));
        })->get();

        return view('livewire.search-profile-show', [
            'articles' => $articles,
            'genres' => $genres,
            'newsOutlets' => $newsOutlets,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedGenre(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedNewsOutlet(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedGenre = [];
        $this->selectedNewsOutlet = [];
        $this->resetPage();
    }
}
