<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\NewsOutlet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'url' => $this->faker->url(),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'news_outlet_id' => NewsOutlet::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Indicate that the article belongs to a specific news outlet.
     */
    public function forNewsOutlet(NewsOutlet $newsOutlet): self
    {
        return $this->state(fn (array $attributes): array => [
            'news_outlet_id' => $newsOutlet->id,
        ]);
    }
}
