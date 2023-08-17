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
        $old = $this->faker->numberBetween(0,$price + ($price * 0.2));

        return [
            "name"      => $this->faker->word(),
            "status_id" => ProductStatusesEnum::Active,
            "old_price" => $old <= $price ? null : $old,
            "price"     => $price,
            "image"     => $this->faker->filePath()
        ];
    }
}
