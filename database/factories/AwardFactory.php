<?php

namespace Database\Factories;

use App\Models\Award;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Award>
 */
class AwardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Award::class;

    public function definition()
    {
        return [
            'name' => $this->faker->firstNameFemale,
            'content' => $this->faker->text($maxNbChars = 200),
            'year'=> '2023',
            'order'=> $this->faker->randomDigitNotNull,
        ];
    }
}
