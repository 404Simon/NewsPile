<?php

use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;
use App\Models\SearchProfile;
use App\Models\User;

test('search profile can be created', function (): void {
    $searchProfile = SearchProfile::factory()->create();

    $this->assertModelExists($searchProfile);
});

test('search profile belongs to a user', function (): void {
    $user = User::factory()->create();
    $searchProfile = SearchProfile::factory()
        ->forUser($user)
        ->create();

    expect($searchProfile->user)->toBeInstanceOf(User::class);
    expect($searchProfile->user->id)->toBe($user->id);
});

test('search profile has genres', function (): void {
    $searchProfile = SearchProfile::factory()->create();
    $genres = Genre::factory(3)->create();

    $searchProfile->genres()->attach($genres);

    expect($searchProfile->genres)->toHaveCount(3);
    expect($searchProfile->genres->first())->toBeInstanceOf(Genre::class);
});

test('search profile has news outlets', function (): void {
    $searchProfile = SearchProfile::factory()->create();
    $newsOutlets = NewsOutlet::factory(3)->create();

    $searchProfile->newsOutlets()->attach($newsOutlets);

    expect($searchProfile->newsOutlets)->toHaveCount(3);
    expect($searchProfile->newsOutlets->first())->toBeInstanceOf(NewsOutlet::class);
});

test('search profile has articles', function (): void {
    $searchProfile = SearchProfile::factory()->create();
    $articles = Article::factory(3)->create();

    // Attach with read_at data
    $articlesData = [];
    foreach ($articles as $article) {
        $articlesData[$article->id] = ['read_at' => now()];
    }
    $searchProfile->articles()->attach($articlesData);

    expect($searchProfile->articles)->toHaveCount(3);
    expect($searchProfile->articles->first())->toBeInstanceOf(Article::class);
    expect($searchProfile->articles->first()->pivot->read_at)->not->toBeNull();
});
