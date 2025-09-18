<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NewsOutlet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<NewsOutlet>
 */
final class NewsOutletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();
        $domain = mb_strtolower(str_replace(' ', '', $name)).'.com';

        return [
            'name' => $name,
            'url' => "https://www.$domain",
            'rss_url' => "https://www.$domain/rss",
            // Generate a simple base64 encoded logo placeholder
            'b64_logo' => 'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="#'.$this->faker->hexColor().'" /></svg>'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
