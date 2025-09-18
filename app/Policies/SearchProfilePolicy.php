<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SearchProfile;
use App\Models\User;

final class SearchProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SearchProfile $searchProfile): bool
    {
        return $user->id === $searchProfile->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SearchProfile $searchProfile): bool
    {
        return $user->id === $searchProfile->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SearchProfile $searchProfile): bool
    {
        return $user->id === $searchProfile->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(): bool
    {
        return false;
    }
}
