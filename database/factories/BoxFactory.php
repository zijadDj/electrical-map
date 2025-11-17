<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BoxFactory extends Factory
{
    protected $model = \App\Models\Box::class;

    public function definition()
    {
        return [
            'code' => strtoupper(Str::random(6)),
            'latitude' => $this->faker->latitude(41.8, 42.5),
            'longitude' => $this->faker->longitude(18.5, 19.5),
            'nameOfConsumer' => $this->faker->company(),
            'numberOfConsumer' => $this->faker->numerify('#######'),
            'status' => $this->faker->randomElement(['read', 'not_read', 'season']),
        ];
    }
}
