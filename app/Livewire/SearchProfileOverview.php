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

    public bool $showDeleteModal = false;

    public ?SearchProfile $profileToDelete = null;

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

    public function confirmDelete(SearchProfile $profile): void
    {
        $this->profileToDelete = $profile;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->profileToDelete) {
            return;
        }

        $this->authorize('delete', $this->profileToDelete);

        $this->profileToDelete->delete();

        $this->resetModal();
    }

    public function cancelDelete(): void
    {
        $this->resetModal();
    }

    private function resetModal(): void
    {
        $this->profileToDelete = null;
        $this->showDeleteModal = false;
    }
}
