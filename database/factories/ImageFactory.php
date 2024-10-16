<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */

class ImageFactory extends Factory
{
        /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $urls = [
            '/images/hoodie.jpg',
            '/images/t-shirt.jpg',
            '/images/mug.jpg',
            '/images/pen.jpg',
        ];
        return [
            'url' => collect($urls)->random(),
            'keep' => 'No',
            'description' => 'text prompt',
            'imageable_id' =>  $this->faker->numberBetween(1, 20),
            'imageable_type' => Str::random(10),
        ];
    }
}
 