<?php

use App\Models\SearchProfile;
use App\Models\User;

test('user can be created', function (): void {
    $user = User::factory()->create();

    $this->assertModelExists($user);
});

test('user has search profiles', function (): void {
    $user = User::factory()->create();
    $searchProfiles = SearchProfile::factory(3)->forUser($user)->create();

    expect($user->searchProfiles)->toHaveCount(3);
    expect($user->searchProfiles->first())->toBeInstanceOf(SearchProfile::class);
});
