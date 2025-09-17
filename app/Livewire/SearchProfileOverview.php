<?php

namespace App\Livewire;

use App\Models\SearchProfile;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class SearchProfileOverview extends Component
{
    use WithPagination;

    #[Title('Search Profiles')]
    public function render(): View
    {
        $searchProfiles = auth()->user()->searchProfiles()
            ->with(['genres', 'newsOutlets'])
            ->latest()
            ->paginate(6);

        return view('livewire.search-profile-overview', [
            'searchProfiles' => $searchProfiles,
        ]);
    }

    public function deleteProfile(SearchProfile $profile): void
    {
        $this->authorize('delete', $profile);
        $profile->delete();
    }
}
