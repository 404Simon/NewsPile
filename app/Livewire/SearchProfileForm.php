<?php

namespace App\Livewire;

use App\Models\Genre;
use App\Models\NewsOutlet;
use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class SearchProfileForm extends Component
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    public array $selectedGenres = [];

    public array $selectedNewsOutlets = [];

    #[Title('Create Search Profile')]
    public function render(): View
    {
        return view('livewire.search-profile-form', [
            'genres' => Genre::orderBy('name')->get(),
            'newsOutlets' => NewsOutlet::orderBy('name')->get(),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        /** @var \App\Models\SearchProfile $profile */
        $profile = auth()->user()->searchProfiles()->create([
            'name' => $this->name,
        ]);

        $profile->genres()->attach($this->selectedGenres);
        $profile->newsOutlets()->attach($this->selectedNewsOutlets);

        $this->redirect(route('profiles', absolute: false), navigate: true);
    }
}
