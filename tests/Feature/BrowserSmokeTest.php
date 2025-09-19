<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SearchProfile;
use App\Models\User;

test('no smoke on guest routes', function () {
    visit(['/', '/login', '/register', '/forgot-password'])
        ->assertNoSmoke()
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors();

});

test('no smoke on authenticated routes', function () {
    $this->actingAs($user = User::factory()->create());

    $searchProfile = SearchProfile::factory()->create(['user_id' => $user->id]);

    visit([
        '/dashboard',
        '/profiles',
        '/profiles/create',
        "/profiles/$searchProfile->id/edit",
        '/confirm-password',
        '/settings/appearance',
        '/settings/password',
        '/settings/profile',
    ])
        ->assertNoSmoke()
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors();
});
