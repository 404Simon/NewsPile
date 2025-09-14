<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SimplePie;

class TagesschauProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

                Log::info('TagesschauProcessor started');
        echo "hi mom";
        // SimplePie initialisieren
        $pie = new SimplePie();

        // Cache-Verzeichnis setzen (muss existieren und beschreibbar sein)
        $cacheDir = storage_path('app/simplepie_cache');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $pie->set_cache_location($cacheDir);
        $pie->set_cache_duration(3600); // 1 Stunde
        $pie->enable_cache(true);

        $pie->set_feed_url('https://www.tagesschau.de/infoservices/alle-meldungen-100~rss2.xml');

        if ($pie->init()) {
            $items = $pie->get_items() ?? [];
            foreach ($items as $item) {
                $title = $item->get_title();
                $link = $item->get_link();
                $description = $item->get_description();
                $pubDate = $item->get_date('Y-m-d H:i:s') ?: now()->toDateTimeString();

                Log::info('Tagesschau Item', [
                    'title' => $title,
                    'link' => $link,
                ]);

                $newsOutlet = NewsOutlet::firstOrCreate(
                    ['name' => 'Tagesschau'],
                    ['url' => 'https://www.tagesschau.de']
                );
                // Beispiel: sichere Massenzuweisung (Article::$fillable muss gesetzt sein)
                try {
                    Article::create([
                        'title' => $title,
                        'url' => $link,
                        'content' => $description,
                        'news_outlet_id' => $newsOutlet->id,
                        'published_at' => $pubDate,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Fehler beim Erstellen des Artikels: ' . $e->getMessage(), [
                        'title' => $title,
                        'link' => $link,
                    ]);
                }
            }
        } else {
            Log::error('SimplePie init failed: ' . $pie->error());
        }
    }
}
