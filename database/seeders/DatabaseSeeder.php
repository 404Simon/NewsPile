<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Genre;
use App\Models\NewsOutlet;
use App\Models\SearchProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create regular user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create additional users
        User::factory(8)->create();

        // Create genres
        $genres = Genre::factory(10)->create();

        // Create news outlets with genres
        $newsOutlets = NewsOutlet::factory(15)->create();
        $newsOutlets->each(function (NewsOutlet $newsOutlet) use ($genres): void {
            // Attach random genres to each news outlet
            $newsOutlet->genres()->attach(
                $genres->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Create articles for this news outlet
            $articlesToCreate = rand(3, 8);
            $articles = Article::factory($articlesToCreate)
                ->forNewsOutlet($newsOutlet)
                ->create();

            // Assign genres to articles that match the news outlet's genres
            $articles->each(function (Article $article) use ($newsOutlet): void {
                // Get a subset of the news outlet's genres
                $newsOutletGenres = $newsOutlet->genres;
                $genresToAttach = $newsOutletGenres->random(
                    min(rand(1, 3), $newsOutletGenres->count())
                )->pluck('id')->toArray();

                $article->genres()->attach($genresToAttach);
            });
        });

        // Create search profiles for users
        $users = User::all();
        $users->each(function (User $user) use ($genres, $newsOutlets): void {
            $searchProfiles = SearchProfile::factory(rand(1, 3))
                ->forUser($user)
                ->create();

            $searchProfiles->each(function (SearchProfile $searchProfile) use ($genres, $newsOutlets): void {
                // Attach random genres
                $searchProfile->genres()->attach(
                    $genres->random(rand(1, 5))->pluck('id')->toArray()
                );

                // Attach random news outlets
                $searchProfile->newsOutlets()->attach(
                    $newsOutlets->random(rand(1, 7))->pluck('id')->toArray()
                );

                // Attach articles from attached news outlets
                $attachedNewsOutlets = $searchProfile->newsOutlets;
                /** @var Collection $articles */
                $articles = Article::whereIn('news_outlet_id', $attachedNewsOutlets->pluck('id'))->get();

                if ($articles->count() > 0) {
                    $articlesToAttach = $articles->random(min(rand(5, 15), $articles->count()))->pluck('id')->toArray();
                    foreach ($articlesToAttach as $articleId) {
                        $searchProfile->articles()->attach($articleId, [
                            'read_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                        ]);
                    }
                }
            });
        });
    }
}
