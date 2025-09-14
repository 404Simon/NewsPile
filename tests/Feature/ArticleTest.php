<?php

use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;

test('article can be created', function (): void {
    $article = Article::factory()->create();

    $this->assertModelExists($article);
});

test('article has genres', function (): void {
    $genres = Genre::factory(3)->create();
    $article = Article::factory()->create();

    $article->genres()->attach($genres);

    expect($article->genres)->toHaveCount(3);
    expect($article->genres->first())->toBeInstanceOf(Genre::class);
});

test('article belongs to a news outlet', function (): void {
    $newsOutlet = NewsOutlet::factory()->create();
    $article = Article::factory()->forNewsOutlet($newsOutlet)->create();

    expect($article->newsOutlet)->toBeInstanceOf(NewsOutlet::class);
    expect($article->newsOutlet->id)->toBe($newsOutlet->id);
});

test('article has required attributes', function (): void {
    $article = Article::factory()->create();

    expect($article)->toHaveKeys(['title', 'content', 'url', 'published_at', 'news_outlet_id']);
});
