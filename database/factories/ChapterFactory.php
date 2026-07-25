<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\Comic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    protected $model = Chapter::class;

    public function definition(): array
    {
        return [
            'comic_id' => Comic::factory(),
            'language' => 'en',
            'volume' => null,
            'chapter' => fake()->numberBetween(1, 100),
            'subchapter' => null,
            'title' => null,
            'slug' => fake()->unique()->slug(),
            'salt' => Str::random(16),
            'prefix' => null,
            'hidden' => 0,
            'licensed' => 0,
            'views' => 0,
            'rating' => null,
            'rating_sum' => 0,
            'rating_count' => 0,
            'download_link' => null,
            'thumbnail' => null,
            'team_id' => null,
            'team2_id' => null,
            'published_on' => now(),
            'publish_start' => now()->subMinute(),
            'publish_end' => null,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => ['hidden' => 1]);
    }

    public function licensed(): static
    {
        return $this->state(fn (array $attributes) => ['licensed' => 1]);
    }

    public function withVolume(int $volume = 1): static
    {
        return $this->state(fn (array $attributes) => ['volume' => $volume]);
    }

    public function withTitle(string $title = 'Test Title'): static
    {
        return $this->state(fn (array $attributes) => ['title' => $title]);
    }
}
