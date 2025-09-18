<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;

test('genre can be created', function (): void {
    $genre = Genre::factory()->create();

    $this->assertModelExists($genre);
});

test('genre has articles', function (): void {
    $genre = Genre::factory()->create();
    $articles = Article::factory(3)->create();

    $genre->articles()->attach($articles);

    expect($genre->articles)->toHaveCount(3);
    expect($genre->articles->first())->toBeInstanceOf(Article::class);
});

test('genre has news outlets', function (): void {
    $genre = Genre::factory()->create();
    $newsOutlets = NewsOutlet::factory(3)->create();

    $genre->newsOutlets()->attach($newsOutlets);

    expect($genre->newsOutlets)->toHaveCount(3);
    expect($genre->newsOutlets->first())->toBeInstanceOf(NewsOutlet::class);
});

test('genre name is required', function (): void {
    expect(fn () => Genre::factory()->create(['name' => null]))
        ->toThrow(Exception::class);
});
