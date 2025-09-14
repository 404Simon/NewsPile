<?php

use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;
use App\Models\SearchProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('search profile can filter articles by multiple criteria', function (): void {
    // Create a user with search profile
    $user = User::factory()->create();
    $searchProfile = SearchProfile::factory()->forUser($user)->create();

    // Create genres
    $techGenre = Genre::factory()->create(['name' => 'Technology']);
    $scienceGenre = Genre::factory()->create(['name' => 'Science']);
    $sportsGenre = Genre::factory()->create(['name' => 'Sports']);

    // Create news outlets with genres
    $techOutlet = NewsOutlet::factory()->create(['name' => 'Tech News']);
    $techOutlet->genres()->attach($techGenre);

    $scienceOutlet = NewsOutlet::factory()->create(['name' => 'Science Daily']);
    $scienceOutlet->genres()->attach($scienceGenre);

    $sportsOutlet = NewsOutlet::factory()->create(['name' => 'Sports Center']);
    $sportsOutlet->genres()->attach($sportsGenre);

    // Create articles for each outlet with appropriate genres
    $techArticles = Article::factory(3)->forNewsOutlet($techOutlet)->create();
    $techArticles->each(fn ($article) => $article->genres()->attach($techGenre));

    $scienceArticles = Article::factory(3)->forNewsOutlet($scienceOutlet)->create();
    $scienceArticles->each(fn ($article) => $article->genres()->attach($scienceGenre));

    $sportsArticles = Article::factory(3)->forNewsOutlet($sportsOutlet)->create();
    $sportsArticles->each(fn ($article) => $article->genres()->attach($sportsGenre));

    // Attach tech and science genres and outlets to search profile
    $searchProfile->genres()->attach([$techGenre->id, $scienceGenre->id]);
    $searchProfile->newsOutlets()->attach([$techOutlet->id, $scienceOutlet->id]);

    // Get articles that match both the genres AND news outlets in the search profile
    $matchingArticles = Article::whereIn('news_outlet_id', $searchProfile->newsOutlets->pluck('id'))
        ->whereHas('genres', function ($query) use ($searchProfile): void {
            $query->whereIn('genres.id', $searchProfile->genres->pluck('id'));
        })
        ->get();

    // Should match all tech and science articles but not sports
    expect($matchingArticles)->toHaveCount(6);
    expect($matchingArticles->pluck('id')->toArray())
        ->toEqual([...$techArticles->pluck('id'), ...$scienceArticles->pluck('id')]);
});

test('search profile can track read articles', function (): void {
    // Create user with search profile
    $user = User::factory()->create();
    $searchProfile = SearchProfile::factory()->forUser($user)->create();

    // Create news outlet with articles
    $newsOutlet = NewsOutlet::factory()->create();
    $articles = Article::factory(6)->forNewsOutlet($newsOutlet)->create();

    // Attach news outlet to search profile
    $searchProfile->newsOutlets()->attach($newsOutlet->id);

    // Mark half of the articles as read
    $readArticles = $articles->take(3);
    foreach ($readArticles as $article) {
        $searchProfile->articles()->attach($article->id, ['read_at' => now()]);
    }

    // Test retrieving read and unread articles
    $readCount = DB::table('search_profile_article')
        ->where('search_profile_id', $searchProfile->id)
        ->whereNotNull('read_at')
        ->count();

    expect($readCount)->toBe(3);

    // Get potential articles for this search profile that haven't been read
    $unreadArticles = Article::whereIn('news_outlet_id', $searchProfile->newsOutlets->pluck('id'))
        ->whereNotIn('id', function ($query) use ($searchProfile): void {
            $query->select('article_id')
                ->from('search_profile_article')
                ->where('search_profile_id', $searchProfile->id)
                ->whereNotNull('read_at');
        })
        ->get();

    // Should include 3 unread articles plus all unattached articles
    expect($unreadArticles)->toHaveCount(3);
});
