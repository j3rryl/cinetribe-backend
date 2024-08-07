<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Media;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faction>
 */
class FactionFactory extends Factory
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
            'media_id' => Media::inRandomOrder()->first()->id ?? null, 
            'description' => $this->faker->paragraph, 
            'thumbnail' => $this->faker->imageUrl, 
        ];
    }
}
