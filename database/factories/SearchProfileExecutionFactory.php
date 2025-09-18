<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SearchProfileExecution>
 */
class SearchProfileExecutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $executedAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'search_profile_id' => \App\Models\SearchProfile::factory(),
            'executed_at' => $executedAt,
            'articles_checked_until' => $executedAt,
            'articles_processed' => fake()->numberBetween(0, 50),
        ];
    }
}
