<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first();
        return [
            "order_id" => Order::query()->inRandomOrder()->first(),
            "product_id" => $product->id,
            "old_price" => 0,
            "price" => $product->price,
            "pay_price" => $product->price,
            "count" => $product->quantity,
        ];
    }
}
