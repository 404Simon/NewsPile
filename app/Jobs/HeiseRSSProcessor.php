<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Article;
use App\Models\NewsOutlet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimplePie\SimplePie;
use Throwable;

final class HeiseRSSProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('HeiseRSSProcessor started');

        $pie = new SimplePie;
        $pie->enable_cache(false);

        $newsOutlet = NewsOutlet::query()->firstOrCreate(
            ['name' => 'Heise'],
            [
                'url' => 'https://www.heise.de',
                'rss_url' => 'https://www.heise.de/rss/heise.rdf',
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

                if (Str::contains($title, ['heise+', 'heise-Angebot'], ignoreCase: true)) {
                    continue;
                }

                $link = $item->get_link();

                if (Str::contains($link, ['bestenlisten'], ignoreCase: true)) {
                    continue;
                }

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
                    HeiseArticleProcessor::dispatch([
                        'title' => $title,
                        'url' => $link,
                        'description' => $description,
                        'news_outlet_id' => $newsOutlet->id,
                        'published_at' => $pubDate,
                    ]);

                    $processedCount++;
                } catch (Throwable $e) {
                    Log::error('Error dispatching article processor: '.$e->getMessage(), [
                        'title' => $title,
                        'link' => $link,
                    ]);
                }
            }

            Log::info('HeiseRSSProcessor completed', [
                'processed' => $processedCount,
                'skipped' => $skippedCount,
                'total' => count($items),
            ]);
        } else {
            Log::error('SimplePie init failed: '.$pie->error());
        }
    }
}
