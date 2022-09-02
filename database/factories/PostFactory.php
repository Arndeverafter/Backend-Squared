<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' =>  $this->faker->paragraphs(12 , true),
            'publication_date' => $this->faker->dateTimeThisMonth(),
            'author' => User::factory()
        ];
    }
}
