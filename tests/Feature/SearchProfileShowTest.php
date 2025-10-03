<?php

declare(strict_types=1);

use App\Livewire\SearchProfileShow;
use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;
use App\Models\SearchProfile;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view search profile with articles', function () {
    $profile = SearchProfile::factory()->create(['user_id' => $this->user->id]);
    $genre = Genre::factory()->create();
    $newsOutlet = NewsOutlet::factory()->create();

    $articles = Article::factory()->count(5)->create([
        'news_outlet_id' => $newsOutlet->id,
    ]);

    foreach ($articles as $article) {
        $article->genres()->attach($genre->id);
    }

    $profile->articles()->attach($articles->pluck('id'));

    $response = $this->get(route('profiles.show', $profile));

    $response->assertSuccessful()
        ->assertSeeLivewire(SearchProfileShow::class);
});

test('can filter articles by search term', function () {
    $profile = SearchProfile::factory()->create(['user_id' => $this->user->id]);
    $genre = Genre::factory()->create();
    $newsOutlet = NewsOutlet::factory()->create();

    $article1 = Article::factory()->create([
        'title' => 'Test Article About Laravel',
        'news_outlet_id' => $newsOutlet->id,
    ]);
    $article1->genres()->attach($genre->id);

    $article2 = Article::factory()->create([
        'title' => 'Article About PHP',
        'news_outlet_id' => $newsOutlet->id,
    ]);
    $article2->genres()->attach($genre->id);

    $profile->articles()->attach([$article1->id, $article2->id]);

    Livewire::test(SearchProfileShow::class, ['searchProfile' => $profile])
        ->set('search', 'Laravel')
        ->assertSee('Test Article About Laravel')
        ->assertDontSee('Article About PHP');
});

test('can filter articles by genre', function () {
    $profile = SearchProfile::factory()->create(['user_id' => $this->user->id]);
    $genre1 = Genre::factory()->create(['name' => 'Technology']);
    $genre2 = Genre::factory()->create(['name' => 'Sports']);
    $newsOutlet = NewsOutlet::factory()->create();

    $article1 = Article::factory()->create([
        'news_outlet_id' => $newsOutlet->id,
    ]);
    $article1->genres()->attach($genre1->id);

    $article2 = Article::factory()->create([
        'news_outlet_id' => $newsOutlet->id,
    ]);
    $article2->genres()->attach($genre2->id);

    $profile->articles()->attach([$article1->id, $article2->id]);

    Livewire::test(SearchProfileShow::class, ['searchProfile' => $profile])
        ->set('selectedGenre', $genre1->id)
        ->assertSee($article1->title)
        ->assertDontSee($article2->title);
});

test('can filter articles by news outlet', function () {
    $profile = SearchProfile::factory()->create(['user_id' => $this->user->id]);
    $genre = Genre::factory()->create();
    $outlet1 = NewsOutlet::factory()->create(['name' => 'TechNews']);
    $outlet2 = NewsOutlet::factory()->create(['name' => 'SportNews']);

    $article1 = Article::factory()->create([
        'news_outlet_id' => $outlet1->id,
    ]);
    $article1->genres()->attach($genre->id);

    $article2 = Article::factory()->create([
        'news_outlet_id' => $outlet2->id,
    ]);
    $article2->genres()->attach($genre->id);

    $profile->articles()->attach([$article1->id, $article2->id]);

    Livewire::test(SearchProfileShow::class, ['searchProfile' => $profile])
        ->set('selectedNewsOutlet', $outlet1->id)
        ->assertSee($article1->title)
        ->assertDontSee($article2->title);
});

test('can clear all filters', function () {
    $profile = SearchProfile::factory()->create(['user_id' => $this->user->id]);
    $genre = Genre::factory()->create();
    $newsOutlet = NewsOutlet::factory()->create();

    $articles = Article::factory()->count(3)->create([
        'news_outlet_id' => $newsOutlet->id,
    ]);

    foreach ($articles as $article) {
        $article->genres()->attach($genre->id);
    }

    $profile->articles()->attach($articles->pluck('id'));

    Livewire::test(SearchProfileShow::class, ['searchProfile' => $profile])
        ->set('search', 'test')
        ->set('selectedGenre', $genre->id)
        ->set('selectedNewsOutlet', $newsOutlet->id)
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('selectedGenre', null)
        ->assertSet('selectedNewsOutlet', null);
});

test('cannot view other users search profiles', function () {
    $otherUser = User::factory()->create();
    $profile = SearchProfile::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('profiles.show', $profile));

    $response->assertForbidden();
});

test('shows empty state when no articles found', function () {
    $profile = SearchProfile::factory()->create(['user_id' => $this->user->id]);

    Livewire::test(SearchProfileShow::class, ['searchProfile' => $profile])
        ->assertSee('No articles found');
});
