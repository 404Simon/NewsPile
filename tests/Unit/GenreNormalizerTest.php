<?php

use App\Models\Genre;
use App\Services\GenreNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Genre::factory()->create([
        'name' => 'Wissenschaft',
        'synonyms' => ['wissenschaftlich', 'science', 'sciences'],
    ]);

    Genre::factory()->create([
        'name' => 'Politik',
        'synonyms' => ['political', 'politics', 'politik'],
    ]);

    Genre::factory()->create([
        'name' => 'Sport',
        'synonyms' => ['sports', 'sportlich'],
    ]);

    $this->normalizer = new GenreNormalizer(maxLevenshteinDistance: 2, minSimilarityPercent: 70.0);
});

it('matches exact name correctly', function () {
    $genre = $this->normalizer->match('Wissenschaft');
    expect($genre)->not->toBeNull();
    expect($genre->name)->toBe('Wissenschaft');
});

it('matches a known synonym exactly', function () {
    $genre = $this->normalizer->match('science');
    expect($genre)->not->toBeNull();
    expect($genre->name)->toBe('Wissenschaft');
});

it('matches with small typo via levenshtein', function () {
    $genre = $this->normalizer->match('wissenschaf');
    expect($genre)->not->toBeNull();
    expect($genre->name)->toBe('Wissenschaft');
});

it('matches English variant politics to Politik', function () {
    $genre = $this->normalizer->match('politics');
    expect($genre)->not->toBeNull();
    expect($genre->name)->toBe('Politik');
});

it('rejects if too different', function () {
    $genre = $this->normalizer->match('completely unrelated string');
    expect($genre)->toBeNull();
});

it('matches synonyms with plural or variant', function () {
    $genre = $this->normalizer->match('sports');
    expect($genre)->not->toBeNull();
    expect($genre->name)->toBe('Sport');
});

it('ignores casing and special characters', function () {
    $genre = $this->normalizer->match('WISSENSCHAFTlich!!!');
    expect($genre)->not->toBeNull();
    expect($genre->name)->toBe('Wissenschaft');
});
