<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->sentence(3), 
            'genre_id' => null, 
            'description' => $this->faker->paragraph, 
            'thumbnail' => $this->faker->imageUrl, 
            'media_type' => $this->faker->randomElement(['movie', 'music', 'sport']),
        ];
    }
}
