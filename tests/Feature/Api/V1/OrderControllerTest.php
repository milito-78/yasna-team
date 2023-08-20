<?php

namespace Tests\Feature\Api\V1;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Infrastructure\Payments\Contracts\PaymentMethod;
use App\Infrastructure\Payments\Factory;
use App\Infrastructure\Payments\Models\PurchaseResult;
use App\Models\Enums\OrderStatusesEnum;
use App\Models\Enums\PaymentGatewayEnum;
use App\Models\Enums\ProductStatusesEnum;
use App\Models\Enums\TransactionStatusEnum;
use App\Models\User;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderItemEntity;
use App\Services\Orders\Entities\StartPaymentResult;
use App\Services\Orders\Entities\TransactionEntity;
use App\Services\Orders\Interfaces\IOrderService;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Interfaces\IProductService;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private function seedDb()
    {
        $this->seed([
            UserSeeder::class
        ]);
    }

    /**
     * Test get list successfully
     */
    public function test_get_list_successfully(): void
    {
        $this->seedDb();
        /**
         * @var User $user
         */
        $user = User::query()->activeUser()->first();

        $this->mock(IOrderService::class,function (MockInterface $mock) use($user){
            return $mock
                ->shouldReceive("GetUserOrders")
                ->once()
                ->withAnyArgs()
                ->andReturn(
                    new CustomSimplePaginate(
                        collect([
                            new OrderEntity(
                                1,
                                $user->id,
                                100,
                                100,
                                null,
                                OrderStatusesEnum::Created,
                                now(),
                                now()
                            )
                        ]),
                        15,
                        1,
                        null
                    )
                );
        });

        $response = $this->withToken($user->email,"Basic")->get("/api/v1/orders");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "success",
            "data" => [
                "items" => [
                    [
                        "id",
                        "total_price",
                        "pay_price",
                        "discount_price",
                        "status",
                        "status_text",
                        "items",
                        "created_at",
                        "updated_at",
                    ]
                ],
                "paginate" => [
                    "is_simple_paginate",
                    "current_page",
                    "first_page_url",
                    "next_page_url",
                    "prev_page_url",
                    "path",
                    "per_page",
                    "from",
                    "to"
                ]
            ]
        ]);
    }

    /**
     * Test get order details
     */
    public function test_get_details_successfully(): void
    {
        $this->seedDb();
        /**
         * @var User $user
         */
        $user = User::query()->activeUser()->first();

        $this->mock(IOrderService::class,function (MockInterface $mock) use($user){
            return $mock
                ->shouldReceive("GetUserOrderDetails")
                ->once()
                ->withAnyArgs()
                ->andReturn(
                        new OrderEntity(
                            1,
                            $user->id,
                            100,
                            100,
                            null,
                            OrderStatusesEnum::Created,
                            now(),
                            now(),
                            collect([
                                new OrderItemEntity(
                                    1,1,1,100,90,1,now(),now()
                                )
                            ])
                        )
                );
        });
        $this->mock(IProductService::class,function (MockInterface $mock) use($user){
            return $mock
                ->shouldReceive("GetProductsInId")
                ->once()
                ->withAnyArgs()
                ->andReturn(
                    collect([new ProductEntity(
                        1,
                        "test",
                        100,
                        90,
                        2,
                        ProductStatusesEnum::Active,
                        "/image/test.png",
                        now(),
                        now()
                    )])
                );
        });

        $response = $this->withToken($user->email,"Basic")->get("/api/v1/orders/1");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "success",
            "data" => [
                "id",
                "total_price",
                "pay_price",
                "discount_price",
                "status",
                "status_text",
                "items" => [
                    [
                        "id",
                        "order_id",
                        "old_price",
                        "price",
                        "count",
                        "product" => [
                            "id",
                            "name",
                            "old_price",
                            "price",
                            "quantity",
                            "image",
                        ],
                    ]
                ],
                "created_at",
                "updated_at",
            ]
        ]);
    }

    /**
     * Test submit new order
     */
    public function test_submit_order_successfully(): void
    {
        $this->seedDb();
        /**
         * @var User $user
         */
        $user = User::query()->activeUser()->first();

        $this->mock(IOrderService::class,function (MockInterface $mock) use($user){
            $mock->shouldReceive("StartPayment")
                ->withAnyArgs()
                ->andReturn(new StartPaymentResult(
                    new TransactionEntity(
                        1,"sdds",1,200,null,null,TransactionStatusEnum::Started,PaymentGatewayEnum::MilitoPayment,null,now(),now()
                    ),"http://redircet.test",true
                ));
            return $mock
                ->shouldReceive("SubmitOrder")
                ->once()
                ->withAnyArgs()
                ->andReturn(
                    new OrderEntity(
                        1,
                        $user->id,
                        100,
                        100,
                        null,
                        OrderStatusesEnum::Created,
                        now(),
                        now(),
                        collect([
                            new OrderItemEntity(
                                1,1,1,100,90,1,now(),now()
                            )
                        ])
                    )
                );
        });

        $this->mock(IProductService::class,function (MockInterface $mock) use($user){
            $mock->shouldReceive("LockProductsCountForReason")->once()->withAnyArgs()->andReturn();
            return $mock
                ->shouldReceive("GetProductsInId")
                ->once()
                ->withAnyArgs()
                ->andReturn(
                    collect([new ProductEntity(
                        1,
                        "test",
                        100,
                        90,
                        2,
                        ProductStatusesEnum::Active,
                        "/image/test.png",
                        now(),
                        now()
                    )])
                );
        });

        $response = $this->withToken($user->email,"Basic")->postJson("/api/v1/orders/create",[
            "items" => [
                [
                "id" => 1,
                "count" => 1
                ]
            ],
            "gateway" => "milito"
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            "message",
            "success",
            "data" => [
                "order" => [
                    "id",
                    "total_price",
                    "pay_price",
                    "discount_price",
                    "status",
                    "status_text",
                    "created_at",
                    "updated_at",
                ],
                "redirect_to_gateway"
            ]
        ]);
    }


}
