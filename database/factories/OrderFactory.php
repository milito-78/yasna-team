<?php

namespace Database\Factories;

use App\Models\Enums\OrderStatusesEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $price = $this->faker->numberBetween(100,99999);

        return [
            "user_id" => User::query()->activeUser()->first()->id,
            "total_price" => $price,
            "pay_price" => $price,
            "discount_price" => 0,
            "status_id" => OrderStatusesEnum::Accepted,
        ];
    }
}
