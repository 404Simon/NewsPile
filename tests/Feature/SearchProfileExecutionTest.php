<?php

declare(strict_types=1);

use App\Jobs\DispatchSearchProfileJobs;
use App\Jobs\ProcessSearchProfile;
use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;
use App\Models\SearchProfile;
use App\Models\SearchProfileExecution;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('process search profile job finds relevant articles on first execution', function () {
    Queue::fake();

    $user = User::factory()->create();
    $genre = Genre::factory()->create();
    $newsOutlet = NewsOutlet::factory()->create();

    $searchProfile = SearchProfile::factory()->create(['user_id' => $user->id]);
    $searchProfile->genres()->attach($genre);
    $searchProfile->newsOutlets()->attach($newsOutlet);

    // Create articles from the last 24 hours
    $recentArticle = Article::factory()->create([
        'news_outlet_id' => $newsOutlet->id,
        'created_at' => now()->subHours(12),
    ]);
    $recentArticle->genres()->attach($genre);

    // Create an old article that shouldn't be included
    $oldArticle = Article::factory()->create([
        'news_outlet_id' => $newsOutlet->id,
        'created_at' => now()->subDays(2),
    ]);
    $oldArticle->genres()->attach($genre);

    $job = new ProcessSearchProfile($searchProfile);
    $job->handle();

    // Check that the recent article was associated
    expect($searchProfile->articles)->toHaveCount(1);
    expect($searchProfile->articles->first()->id)->toBe($recentArticle->id);

    // Check that execution was recorded
    $execution = SearchProfileExecution::query()->where('search_profile_id', $searchProfile->id)->first();
    expect($execution)->not->toBeNull();
    expect($execution->articles_processed)->toBe(1);
});

test('process search profile job only processes new articles on subsequent executions', function () {
    Queue::fake();

    $user = User::factory()->create();
    $genre = Genre::factory()->create();
    $searchProfile = SearchProfile::factory()->create(['user_id' => $user->id]);
    $searchProfile->genres()->attach($genre);

    // Create first execution
    $lastExecution = SearchProfileExecution::factory()->create([
        'search_profile_id' => $searchProfile->id,
        'executed_at' => now()->subHour(),
        'articles_checked_until' => now()->subHour(),
    ]);

    // Create an article before the last execution (shouldn't be processed)
    $oldArticle = Article::factory()->create([
        'created_at' => now()->subHours(2),
    ]);
    $oldArticle->genres()->attach($genre);

    // Create a new article after the last execution (should be processed)
    $newArticle = Article::factory()->create([
        'created_at' => now()->subMinutes(30),
    ]);
    $newArticle->genres()->attach($genre);

    $job = new ProcessSearchProfile($searchProfile);
    $job->handle();

    // Check that only the new article was associated
    expect($searchProfile->articles)->toHaveCount(1);
    expect($searchProfile->articles->first()->id)->toBe($newArticle->id);

    // Check that a new execution was recorded
    $executions = SearchProfileExecution::query()->where('search_profile_id', $searchProfile->id)->get();
    expect($executions)->toHaveCount(2);
    expect($executions->sortByDesc('executed_at')->first()->articles_processed)->toBe(1);
});

test('dispatch search profile jobs dispatches job for each search profile', function () {
    Queue::fake();

    $searchProfiles = SearchProfile::factory()->count(3)->create();

    $job = new DispatchSearchProfileJobs;
    $job->handle();

    Queue::assertPushed(ProcessSearchProfile::class, 3);

    foreach ($searchProfiles as $searchProfile) {
        Queue::assertPushed(ProcessSearchProfile::class, function ($job) use ($searchProfile) {
            return $job->searchProfile->id === $searchProfile->id;
        });
    }
});

test('search profile filters articles by genres and news outlets', function () {
    Queue::fake();

    $user = User::factory()->create();
    $genre1 = Genre::factory()->create();
    $genre2 = Genre::factory()->create();
    $newsOutlet1 = NewsOutlet::factory()->create();
    $newsOutlet2 = NewsOutlet::factory()->create();

    $searchProfile = SearchProfile::factory()->create(['user_id' => $user->id]);
    $searchProfile->genres()->attach($genre1);
    $searchProfile->newsOutlets()->attach($newsOutlet1);

    // Create articles with different combinations
    $matchingArticle = Article::factory()->create([
        'news_outlet_id' => $newsOutlet1->id,
        'created_at' => now()->subHours(12),
    ]);
    $matchingArticle->genres()->attach($genre1);

    $wrongGenreArticle = Article::factory()->create([
        'news_outlet_id' => $newsOutlet1->id,
        'created_at' => now()->subHours(12),
    ]);
    $wrongGenreArticle->genres()->attach($genre2);

    $wrongOutletArticle = Article::factory()->create([
        'news_outlet_id' => $newsOutlet2->id,
        'created_at' => now()->subHours(12),
    ]);
    $wrongOutletArticle->genres()->attach($genre1);

    $job = new ProcessSearchProfile($searchProfile);
    $job->handle();

    // Only the matching article should be associated
    expect($searchProfile->articles)->toHaveCount(1);
    expect($searchProfile->articles->first()->id)->toBe($matchingArticle->id);
});

test('search profile works when no genre or news outlet filters are specified', function () {
    Queue::fake();

    $user = User::factory()->create();
    $searchProfile = SearchProfile::factory()->create(['user_id' => $user->id]);

    // Create articles without any specific filters
    $article1 = Article::factory()->create(['created_at' => now()->subHours(12)]);
    $article2 = Article::factory()->create(['created_at' => now()->subHours(6)]);

    $job = new ProcessSearchProfile($searchProfile);
    $job->handle();

    // All recent articles should be associated
    expect($searchProfile->articles)->toHaveCount(2);

    $execution = SearchProfileExecution::query()->where('search_profile_id', $searchProfile->id)->first();
    expect($execution->articles_processed)->toBe(2);
});
