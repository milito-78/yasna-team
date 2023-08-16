<?php

namespace Database\Factories;

use App\Models\ProductChange;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductChangeFactory extends Factory
{
    protected $model = ProductChange::class;

    public function definition(): array
    {
        return [
            'count' => $this->faker->numberBetween(1,50)
        ];
    }
}
