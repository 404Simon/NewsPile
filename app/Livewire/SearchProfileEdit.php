<?php

namespace App\Livewire;

use App\Models\Genre;
use App\Models\NewsOutlet;
use App\Models\SearchProfile;
use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class SearchProfileEdit extends Component
{
    public SearchProfile $searchProfile;

    #[Rule('required|string|max:255')]
    public string $name = '';

    public array $selectedGenres = [];

    public array $selectedNewsOutlets = [];

    public function mount(SearchProfile $searchProfile): void
    {
        $this->authorize('update', $searchProfile);

        $this->searchProfile = $searchProfile;
        $this->name = $searchProfile->name;
        $this->selectedGenres = $searchProfile->genres->pluck('id')->toArray();
        $this->selectedNewsOutlets = $searchProfile->newsOutlets->pluck('id')->toArray();
    }

    #[Title('Edit Search Profile')]
    public function render(): View
    {
        return view('livewire.search-profile-edit', [
            'genres' => Genre::orderBy('name')->get(),
            'newsOutlets' => NewsOutlet::orderBy('name')->get(),
        ]);
    }

    public function save(): void
    {
        $this->authorize('update', $this->searchProfile);
        $this->validate();

        $this->searchProfile->update(['name' => $this->name]);
        $this->searchProfile->genres()->sync($this->selectedGenres);
        $this->searchProfile->newsOutlets()->sync($this->selectedNewsOutlets);

        $this->redirect(route('profiles', absolute: false), navigate: true);
    }
}
