<?php

namespace Tests\Feature\Api\V1;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\ProductStatusesEnum;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Interfaces\IProductService;
use Mockery\MockInterface;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{


    /**
     * Test get list successfully
     */
    public function test_get_list_successfully(): void
    {
        $this->mock(IProductService::class,function (MockInterface $mock){
            return $mock
                ->shouldReceive("GetListWithFilter")
                ->once()
                ->withAnyArgs()
                ->andReturn(
                    new CustomSimplePaginate(
                        collect([
                            new ProductEntity(
                                1,
                                "test",
                                null,
                                100,
                                1,
                                ProductStatusesEnum::Active,
                                "/image/test.png",
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

        $response = $this->get("/api/v1/products");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "success",
            "data" => [
                "items" => [
                    [
                        "id",
                        "name",
                        "old_price",
                        "price",
                        "quantity",
                        "image"
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
     * Test get product detail successfully
     */
    public function test_get_product_detail_successfully()
    {
        $this->mock(IProductService::class,function (MockInterface $mock){
            return $mock
                ->shouldReceive("GetById")
                ->once()
                ->with(1)
                ->andReturn(
                    new ProductEntity(
                        1,
                        "test",
                        null,
                        100,
                        1,
                        ProductStatusesEnum::Active,
                        "/image/test.png",
                        now(),
                        now()
                    )
                );
        });

        $response = $this->get("/api/v1/products/1");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "success",
            "data" => [
                "id",
                "name",
                "old_price",
                "price",
                "quantity",
                "image"
            ],
        ]);
    }

    /**
     * Test undefined product with id
     */
    public function test_get_product_detail_failed()
    {
        $this->mock(IProductService::class,function (MockInterface $mock){
            return $mock
                ->shouldReceive("GetById")
                ->once()
                ->withAnyArgs()
                ->andReturn(null);
        });

        $response = $this->get("/api/v1/products/1");
        $response->assertStatus(404);
        $response->assertJsonStructure([
            "message",
            "success",
        ]);
    }
}
