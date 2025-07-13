<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(20),
            'price' => $this->faker->numberBetween(100,999999),
            'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            'condition_id' => $this->faker->numberBetween(1,4),
            'brand' => $this->faker->company(),
            'description' => $this->faker->realText(200),
            'user_id' => $this->faker->numberBetween(1,2),

        ];
    }
}
