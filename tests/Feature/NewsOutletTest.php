<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;

test('news outlet can be created', function (): void {
    $newsOutlet = NewsOutlet::factory()->create();

    $this->assertModelExists($newsOutlet);
});

test('news outlet has genres', function (): void {
    $newsOutlet = NewsOutlet::factory()->create();
    $genres = Genre::factory(3)->create();

    $newsOutlet->genres()->attach($genres);

    expect($newsOutlet->genres)->toHaveCount(3);
    expect($newsOutlet->genres->first())->toBeInstanceOf(Genre::class);
});

test('news outlet has articles', function (): void {
    $newsOutlet = NewsOutlet::factory()->create();
    $articles = Article::factory(3)->forNewsOutlet($newsOutlet)->create();

    expect($newsOutlet->articles)->toHaveCount(3);
    expect($newsOutlet->articles->first())->toBeInstanceOf(Article::class);

    // All articles should belong to this news outlet
    $newsOutlet->articles->each(function ($article) use ($newsOutlet): void {
        expect($article->news_outlet_id)->toBe($newsOutlet->id);
    });
});

test('news outlet has required attributes', function (): void {
    $newsOutlet = NewsOutlet::factory()->create();

    expect($newsOutlet)->toHaveKeys(['name', 'url', 'rss_url', 'b64_logo']);
});
