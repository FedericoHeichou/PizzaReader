<?php

namespace Database\Factories;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comic>
 */
class ComicFactory extends Factory
{
    protected $model = Comic::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'salt' => Str::random(16),
            'hidden' => 0,
            'author' => fake()->name(),
            'artist' => fake()->name(),
            'target' => fake()->randomElement(['Shonen', 'Seinen', 'Shojo', 'Kodomo']),
            'genres' => 'Action, Adventure',
            'status' => fake()->randomElement(['Ongoing', 'Finished', 'Hiatus']),
            'description' => fake()->paragraph(),
            'thumbnail' => null,
            'custom_chapter' => null,
            'comic_format_id' => 1,
            'adult' => 0,
            'order_index' => 0,
            'alt_titles' => null,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => ['hidden' => 1]);
    }

    public function recommended(): static
    {
        return $this->state(fn (array $attributes) => ['order_index' => fake()->randomFloat(2, 0.1, 10)]);
    }
}
