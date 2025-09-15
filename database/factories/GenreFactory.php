<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = [
            'Politics', 'Technology', 'Science', 'Health', 'Sports',
            'Entertainment', 'Business', 'World News', 'Opinion',
            'Education', 'Environment', 'Travel', 'Food', 'Art',
            'Music', 'Fashion', 'Lifestyle', 'Crime', 'Finance',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($genres),
            'synonyms' => [],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
