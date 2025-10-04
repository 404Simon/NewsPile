<?php

declare(strict_types=1);

use App\Livewire\ArticleView;
use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;
use Livewire\Livewire;

test('can view an article with all details', function () {
    $newsOutlet = NewsOutlet::factory()->create(['name' => 'Test News']);
    $genres = Genre::factory(2)->create();

    $article = Article::factory()->create([
        'title' => 'Test Article Title',
        'description' => 'This is a test article description.',
        'content' => '# Test Content\n\nThis is some test content in markdown.',
        'url' => 'https://example.com/article',
        'published_at' => now()->subDays(2),
        'news_outlet_id' => $newsOutlet->id,
    ]);

    $article->genres()->attach($genres);

    Livewire::test(ArticleView::class, ['article' => $article])
        ->assertSee($article->title)
        ->assertSee($article->description)
        ->assertSee($newsOutlet->name)
        ->assertSee($genres->first()->name)
        ->assertSee($genres->last()->name)
        ->assertSee($article->url)
        ->assertSee('Test Content');
});

test('can access article view page', function () {
    $article = Article::factory()->create();

    $response = $this->get(route('articles.show', $article));

    $response->assertSuccessful();
    $response->assertSeeLivewire(ArticleView::class);
});

test('article view displays properly with minimal data', function () {
    $article = Article::factory()->create([
        'title' => 'Minimal Article',
        'description' => null,
        'content' => 'Basic content', // content is required
        'url' => 'https://example.com', // url is required
        'news_outlet_id' => null,
    ]);

    Livewire::test(ArticleView::class, ['article' => $article])
        ->assertSee($article->title)
        ->assertSee('-') // Should show placeholders for missing data
        ->assertSee('View Original'); // Button should appear since URL exists
});

test('article view page title is set correctly', function () {
    $article = Article::factory()->create(['title' => 'My Test Article']);

    $response = $this->get(route('articles.show', $article));

    $response->assertSeeInOrder(['<title>', 'My Test Article', '</title>'], false);
});
