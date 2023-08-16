<?php

namespace Database\Factories;

use App\Models\Enums\ProductStatusesEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $price = $this->faker->numberBetween(100,99999);
        return [
            "name"      => $this->faker->word(),
            "status_id" => ProductStatusesEnum::Active,
            "old_price" => $this->faker->numberBetween(0,$price / 2),
            "price"     => $price,
            "image"     => $this->faker->filePath()
        ];
    }
}
