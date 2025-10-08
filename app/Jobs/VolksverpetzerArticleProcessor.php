<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Article;
use App\Services\GenreNormalizer;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\HTMLToMarkdown\HtmlConverter;
use Throwable;

final class VolksverpetzerArticleProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private array $articleData
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = $this->articleData['url'];
        $title = $this->articleData['title'];

        if (Article::query()->where('url', $url)->exists()) {
            Log::info('Skipping existing article in processor', [
                'title' => $title,
                'url' => $url,
            ]);

            return;
        }

        Log::info('Processing article content', [
            'title' => $title,
            'url' => $url,
        ]);

        try {
            $response = Http::withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')
                ->get($url);

            if ($response->successful()) {
                $html = $response->body();

                $dom = new DOMDocument;
                @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
                $xpath = new DOMXPath($dom);

                $content = '';

                $articleNode = $xpath->query("//div[@class='et_pb_module et_pb_post_content et_pb_post_content_0_tb_body']")->item(0);

                if ($articleNode instanceof DOMElement) {
                    // Save article node
                    $content = $dom->saveHTML($articleNode);
                }

                $genres = $this->extractGenresFromHTML($xpath);
                $normalizedGenres = $this->normalizeGenres($genres);

                if ($content !== '' && $content !== '0') {
                    $converter = new HtmlConverter(['strip_tags' => true, 'header_style' => 'atx']);
                    $markdown = $converter->convert($content);

                    $markdown = mb_trim($markdown);
                    $markdown = preg_replace("/\n{3,}/", "\n\n", $markdown);

                    $article = Article::query()->create([
                        'title' => $this->articleData['title'],
                        'url' => $this->articleData['url'],
                        'description' => $this->articleData['description'],
                        'content' => $markdown,
                        'news_outlet_id' => $this->articleData['news_outlet_id'],
                        'published_at' => $this->articleData['published_at'],
                    ]);

                    if ($normalizedGenres->isNotEmpty()) {
                        $article->genres()->attach($normalizedGenres->pluck('id'));

                        Log::info('Genres attached to article', [
                            'article_id' => $article->id,
                            'genres' => $normalizedGenres->pluck('name'),
                        ]);
                    }

                    Log::info('Article created successfully with content', [
                        'id' => $article->id,
                        'title' => $article->title,
                        'genre_count' => $normalizedGenres->count(),
                    ]);
                } else {
                    Log::warning('No content found for article, skipping creation', [
                        'title' => $title,
                        'url' => $url,
                    ]);
                }
            } else {
                Log::error('Failed to fetch article content', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);

                // Throw an exception to trigger job retry
                throw new Exception('HTTP request failed with status '.$response->status());
            }
        } catch (Throwable $e) {
            Log::error('Error processing article content: '.$e->getMessage(), [
                'url' => $url,
                'exception' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            // Rethrow the exception to handle retries
            throw $e;
        }
    }

    /**
     * Extract genres from the article HTML via DOMXPath
     */
    private function extractGenresFromHTML(DOMXPath $xpath): Collection
    {
        $genres = collect();

        try {
            $topicNodes = $xpath->query("//p[@class='et_pb_title_meta_container']/a");

            if ($topicNodes && $topicNodes->length > 0) {
                foreach ($topicNodes as $node) {
                    $genreName = mb_trim($node->textContent ?? '');

                    if ($genreName !== '') {
                        $genres->push($genreName);
                    }
                }
            }

        } catch (Throwable $e) {
            Log::error('Failed to extract genres from HTML: '.$e->getMessage(), [
                'exception' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $genres->unique()->values();
    }

    /**
     * Normalize genres using the GenreNormalizer service
     *
     * @param  Collection<array-key,mixed>  $rawGenres
     */
    private function normalizeGenres(Collection $rawGenres): Collection
    {
        $normalizer = app(GenreNormalizer::class);

        return $rawGenres
            ->map(fn (string $rawGenre) => $normalizer->match($rawGenre))
            ->filter()
            ->unique('id');
    }
}
