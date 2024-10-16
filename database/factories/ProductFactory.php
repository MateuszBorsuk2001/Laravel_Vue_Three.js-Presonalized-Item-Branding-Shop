<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $names = [
            'Pen',
            'Hoodie',
            'T-shirt',
            'Mug',
        ];
        return [
            'name' => collect($names)->random(),
            'user_id' => User::inRandomOrder()->first()->id,
            'image_id' => Image::inRandomOrder()->first()->id,
        ];
    }
}
