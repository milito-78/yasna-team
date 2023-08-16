<?php

namespace Database\Seeders;

use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use App\Models\Enums\ProductStatusesEnum;
use App\Models\Product;
use App\Models\ProductChange;
use App\Models\ProductChangeReason;
use App\Models\ProductStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ProductStatusesEnum::cases() as $status){
            ProductStatus::query()->firstOrCreate([
                "id" => $status->value,
                "title" => $status->name
            ],[
                "id" => $status->value,
                "title" => $status->name
            ]);
        }

        foreach (ProductChangeReasonsEnum::cases() as $reasons){
            ProductChangeReason::query()->firstOrCreate([
                "id" => $reasons->value,
                "title" => $reasons->name
            ],[
                "id" => $reasons->value,
                "title" => $reasons->name
            ]);
        }

        Product::factory(20)
            ->has(
                    ProductChange::factory()
                        ->count(1)
                        ->state(function (array $attributes, Product $product) {
                            return [
                                'type'              => ProductChangeTypesEnum::IncreaseVolume,
                                'status'            => ProductChangeStatusesEnum::Increase,
                                'reason_id'         => ProductChangeReasonsEnum::System
                            ];
                        }),
                    "changes"
                )
            ->create();

    }

}
