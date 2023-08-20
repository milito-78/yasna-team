<?php

namespace Database\Seeders;

use App\Models\Enums\OrderStatusesEnum;
use App\Models\Enums\PaymentGatewayEnum;
use App\Models\Enums\TransactionStatusEnum;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentGateway;
use App\Models\TransactionStatus;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (OrderStatusesEnum::cases() as $status){
            OrderStatus::query()->firstOrCreate([
                "id" => $status->value,
                "title" => $status->name
            ],[
                "id" => $status->value,
                "title" => $status->name
            ]);
        }

        foreach (TransactionStatusEnum::cases() as $status){
            TransactionStatus::query()->firstOrCreate([
                "id" => $status->value,
                "title" => $status->name
            ],[
                "id" => $status->value,
                "title" => $status->name
            ]);
        }

        foreach (PaymentGatewayEnum::cases() as $status){
            PaymentGateway::query()->firstOrCreate([
                "id" => $status->value,
                "title" => $status->name
            ],[
                "id" => $status->value,
                "title" => $status->name
            ]);
        }

        Order::factory(20)
            ->has(
                OrderItem::factory()
                    ->count(2),
                "items"
            )
            ->create();
    }
}
