<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\NewsOutlet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SimplePie\SimplePie;

class TagesschauRSSProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('TagesschauRSSProcessor started');

        $pie = new SimplePie;

        $cacheDir = storage_path('app/simplepie_cache');
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        if (! is_writable($cacheDir)) {
            chmod($cacheDir, 0755);

            if (! is_writable($cacheDir)) {
                Log::error("Cache directory is not writable: {$cacheDir}");
                // Use system temp directory as fallback
                $cacheDir = sys_get_temp_dir().'/simplepie_cache';
                if (! is_dir($cacheDir)) {
                    mkdir($cacheDir, 0755, true);
                }
            }
        }

        $pie->set_cache_location($cacheDir);
        $pie->set_cache_duration(3600); // 1 hour
        $pie->enable_cache(true);

        $newsOutlet = NewsOutlet::query()->firstOrCreate(
            ['name' => 'Tagesschau'],
            [
                'url' => 'https://www.tagesschau.de',
                'rss_url' => 'https://www.tagesschau.de/infoservices/alle-meldungen-100~rss2.xml',
                'b64_logo' => 'b64logohiertodo',
            ]
        );

        $pie->set_feed_url($newsOutlet->rss_url);

        // Disable built-in cURL to use PHP's HTTP streams instead
        $pie->force_fsockopen(true);

        if ($pie->init()) {
            $items = $pie->get_items();
            $processedCount = 0;
            $skippedCount = 0;

            foreach ($items as $item) {
                $title = $item->get_title();
                $link = $item->get_link();
                $description = $item->get_description();
                $pubDate = $item->get_date('Y-m-d H:i:s') ?: now()->toDateTimeString();

                if (Article::query()->where('url', $link)->exists()) {
                    Log::info('Skipping existing article', [
                        'title' => $title,
                        'link' => $link,
                    ]);
                    $skippedCount++;

                    continue;
                }

                try {
                    TagesschauArticleProcessor::dispatch([
                        'title' => $title,
                        'url' => $link,
                        'description' => $description,
                        'news_outlet_id' => $newsOutlet->id,
                        'published_at' => $pubDate,
                    ]);

                    $processedCount++;
                } catch (\Throwable $e) {
                    Log::error('Error dispatching article processor: '.$e->getMessage(), [
                        'title' => $title,
                        'link' => $link,
                    ]);
                }
            }

            Log::info('TagesschauRSSProcessor completed', [
                'processed' => $processedCount,
                'skipped' => $skippedCount,
                'total' => count($items),
            ]);
        } else {
            Log::error('SimplePie init failed: '.$pie->error());
        }
    }
}
