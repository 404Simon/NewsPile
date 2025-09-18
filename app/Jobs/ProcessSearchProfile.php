<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\SearchProfile;
use App\Models\SearchProfileExecution;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSearchProfile implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SearchProfile $searchProfile
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        $latestExecution = $this->searchProfile->latestExecution();

        $searchFrom = $latestExecution
            ? $latestExecution->articles_checked_until
            : $now->copy()->subDay(); // First execution: look back 24 hours

        $articlesQuery = Article::query()
            ->where('created_at', '>', $searchFrom)
            ->where('created_at', '<=', $now);

        if ($this->searchProfile->genres()->count() > 0) {
            $genreIds = $this->searchProfile->genres()->pluck('genres.id');
            $articlesQuery->whereHas('genres', function ($query) use ($genreIds) {
                $query->whereIn('genres.id', $genreIds);
            });
        }

        if ($this->searchProfile->newsOutlets()->count() > 0) {
            $newsOutletIds = $this->searchProfile->newsOutlets()->pluck('news_outlets.id');
            $articlesQuery->whereIn('news_outlet_id', $newsOutletIds);
        }

        $articles = $articlesQuery->get();
        $articlesProcessed = 0;

        foreach ($articles as $article) {
            if (! $this->searchProfile->articles()->where('article_id', $article->id)->exists()) {
                $this->searchProfile->articles()->attach($article->id);
                $articlesProcessed++;
            }
        }

        SearchProfileExecution::query()->create([
            'search_profile_id' => $this->searchProfile->id,
            'executed_at' => $now,
            'articles_checked_until' => $now,
            'articles_processed' => $articlesProcessed,
        ]);
    }
}
