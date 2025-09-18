<?php

namespace Tests\Feature\Commands;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tempJsonPath = storage_path('app/testing/genres-test.json');

    if (! File::exists(dirname($this->tempJsonPath))) {
        File::makeDirectory(dirname($this->tempJsonPath), 0755, true);
    }
});

afterEach(function () {
    if (File::exists($this->tempJsonPath)) {
        File::delete($this->tempJsonPath);
    }
});

test('sync genres command creates new genres', function () {
    // arrange
    $genres = [
        ['name' => 'TestGenre1', 'synonyms' => ['test1', 'genre1']],
        ['name' => 'TestGenre2', 'synonyms' => ['test2', 'genre2']],
    ];

    File::put($this->tempJsonPath, json_encode($genres));

    // act & assert
    $this->artisan('app:sync-genres', [
        '--path' => $this->tempJsonPath,
        '--force' => true,
    ])->assertSuccessful();

    $this->assertDatabaseCount('genres', 2);
    $this->assertDatabaseHas('genres', ['name' => 'TestGenre1']);
    $this->assertDatabaseHas('genres', ['name' => 'TestGenre2']);

    $genre = Genre::query()->where('name', 'TestGenre1')->first();
    expect($genre)->not->toBeNull();
    $synonyms = $genre->synonyms;
    expect($synonyms)->toBeArray();
    expect($synonyms)->toContain('test1');
    expect($synonyms)->toContain('genre1');
});

test('sync genres command updates existing genres', function () {
    // arrange
    Genre::query()->create([
        'name' => 'ExistingGenre',
        'synonyms' => ['old1', 'old2'],
    ]);

    $genres = [
        ['name' => 'ExistingGenre', 'synonyms' => ['new1', 'new2']],
    ];
    File::put($this->tempJsonPath, json_encode($genres));

    // act & assert
    $this->artisan('app:sync-genres', [
        '--path' => $this->tempJsonPath,
        '--force' => true,
    ])->assertSuccessful();

    $this->assertDatabaseCount('genres', 1);

    $genre = Genre::query()->where('name', 'ExistingGenre')->first();
    expect($genre)->not->toBeNull();

    $synonyms = $genre->synonyms;
    expect($synonyms)->toBeArray();

    expect(count($synonyms))->toBe(2);
    expect($synonyms)->toContain('new1');
    expect($synonyms)->toContain('new2');
});

test('sync genres command deletes genres not in json', function () {
    // arrange
    Genre::query()->create(['name' => 'KeepGenre', 'synonyms' => ['keep']]);
    Genre::query()->create(['name' => 'DeleteGenre', 'synonyms' => ['delete']]);

    $genres = [
        ['name' => 'KeepGenre', 'synonyms' => ['keep']],
    ];
    File::put($this->tempJsonPath, json_encode($genres));

    // act & assert
    $this->artisan('app:sync-genres', [
        '--path' => $this->tempJsonPath,
        '--force' => true,
    ])->assertSuccessful();

    $this->assertDatabaseCount('genres', 1);
    $this->assertDatabaseHas('genres', ['name' => 'KeepGenre']);
    $this->assertDatabaseMissing('genres', ['name' => 'DeleteGenre']);
});

test('sync genres command fails with invalid json', function () {
    // arrange
    $invalidJson = '{"name": "InvalidJSON", "synonyms": [}';
    File::put($this->tempJsonPath, $invalidJson);

    // act
    $this->artisan('app:sync-genres', [
        '--path' => $this->tempJsonPath,
    ])->assertFailed();

    // assert
    $this->assertDatabaseCount('genres', 0);
});

test('sync genres command fails with empty file', function () {
    // arrange
    File::put($this->tempJsonPath, '');

    // act & assert
    $this->artisan('app:sync-genres', [
        '--path' => $this->tempJsonPath,
    ])->assertFailed();

    $this->assertDatabaseCount('genres', 0);
});

test('sync genres command fails with invalid genre data', function () {
    // arrange
    $invalidGenres = [
        ['synonyms' => ['missing', 'name']],
    ];
    File::put($this->tempJsonPath, json_encode($invalidGenres));

    // act & assert
    $this->artisan('app:sync-genres', [
        '--path' => $this->tempJsonPath,
    ])->assertFailed();

    $this->assertDatabaseCount('genres', 0);
});

test('sync genres command fails when file not found', function () {
    $this->artisan('app:sync-genres', [
        '--path' => 'non/existent/path.json',
    ])->assertFailed();

    $this->assertDatabaseCount('genres', 0);
});
