<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\GenreNormalizer;
use DOMElement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;

class TagesschauArticleProcessor implements ShouldQueue
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
     * The article data to process.
     */
    protected array $articleData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $articleData)
    {
        $this->articleData = $articleData;
    }

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

                $dom = new \DOMDocument;
                @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
                $xpath = new \DOMXPath($dom);

                $content = '';
                $elements = $xpath->query('//article//p[contains(@class, "textabsatz")] | //article//h2');

                if ($elements && $elements->length > 0) {
                    foreach ($elements as $element) {
                        $content .= $dom->saveHTML($element);
                    }
                }

                $htmlGenres = $this->extractGenresFromHtml($xpath);
                $urlGenres = $this->extractGenresFromUrl($url);
                $allRawGenres = $htmlGenres->merge($urlGenres)->unique();
                $normalizedGenres = $this->normalizeGenres($allRawGenres);

                if (! empty($content)) {
                    $converter = new HtmlConverter(['strip_tags' => true, 'header_style' => 'atx']);
                    $markdown = $converter->convert($content);

                    // Process links in the markdown to add the base URL
                    $markdown = $this->processMarkdownLinks($markdown);

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
                throw new \Exception('HTTP request failed with status '.$response->status());
            }
        } catch (\Throwable $e) {
            Log::error('Error processing article content: '.$e->getMessage(), [
                'url' => $url,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rethrow the exception to handle retries
            throw $e;
        }
    }

    /**
     * Extract genres from HTML taglist
     */
    protected function extractGenresFromHtml(\DOMXPath $xpath): Collection
    {
        $tagElements = $xpath->query('//ul[contains(@class, "taglist")]//li[contains(@class, "taglist__element")]//a[contains(@class, "tag-btn")]');

        return collect($tagElements)
            ->map(fn (DOMElement $element) => trim($element->textContent))
            ->filter(fn (string $text) => ! empty($text));
    }

    /**
     * Extract genres from URL segments
     */
    protected function extractGenresFromUrl(string $url): Collection
    {
        $parsedUrl = parse_url($url);

        if (! isset($parsedUrl['path'])) {
            return collect();
        }

        $path = $parsedUrl['path'];

        return collect(explode('/', $path))
            ->filter()
            ->filter(fn (string $segment) => ! Str::contains($segment, '.html') && strlen($segment) > 2);
    }

    /**
     * Normalize genres using the GenreNormalizer service
     */
    protected function normalizeGenres(Collection $rawGenres): Collection
    {
        $normalizer = app(GenreNormalizer::class);

        return $rawGenres
            ->map(fn (string $rawGenre) => $normalizer->match($rawGenre))
            ->filter()
            ->unique('id');
    }

    /**
     * Process markdown content to convert relative links to absolute links
     */
    protected function processMarkdownLinks(string $markdown): string
    {
        // Pattern to match markdown links with relative URLs starting with '/'
        $pattern = '/\[(.*?)\]\((\/[^\s"]+)(\s+".*?")?\)/';

        // Define the base URL for Tagesschau
        $baseUrl = 'https://tagesschau.de';

        // Use Laravel's pipe method to apply the transformation
        return str($markdown)
            ->pipe(fn ($content) => preg_replace_callback(
                $pattern,
                fn ($matches) => sprintf(
                    '[%s](%s%s%s)',
                    $matches[1],             // Link text
                    $baseUrl,                // Base URL
                    $matches[2],             // URL path
                    $matches[3] ?? ''        // Optional title attribute
                ),
                $content
            ));
    }
}
