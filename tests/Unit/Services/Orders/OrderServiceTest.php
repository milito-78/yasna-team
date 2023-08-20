<?php

namespace Tests\Unit\Services\Orders;


use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\OrderStatusesEnum;
use App\Services\Orders\Entities\OrderCreateInput;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\SubmitOrderInput;
use App\Services\Orders\Exceptions\FailedToCreateException;
use App\Services\Orders\Exceptions\OrderNotFoundException;
use App\Services\Orders\Interfaces\IOrderRepository;
use App\Services\Orders\Services\OrderService;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{

    private function getData(Carbon $time):array{
        return [
            new OrderEntity(
                1,
                1,
                200,
                190,
                10,
                OrderStatusesEnum::Created,
                $time,$time,
                collect()
            ),
        ];
    }


    /**
     * Test get user orders service
     */
    public function test_get_user_orders(): void
    {
        $this->mock(IOrderRepository::class,function (MockInterface $mock){
            return $mock->shouldReceive("getOrderListForUserWithPaginate")
                ->withAnyArgs()->once()->andReturn(
                    customSimplePaginator(
                        collect($this->getData(now())),
                        15,
                        1
                    )
                );
        });

        $orderService = $this->app->make(OrderService::class);
        $filter = new OrderFilterInput(15,1);
        $orders = $orderService->GetUserOrders(1,$filter);
        $this->assertInstanceOf(CustomSimplePaginate::class,$orders);
        $this->assertInstanceOf(OrderEntity::class,$orders->items()->first());
    }

    /**
     * Test get user's order
     */
    public function test_get_user_order_details()
    {
        $this->mock(IOrderRepository::class,function (MockInterface $mock){
            return $mock->shouldReceive("getOrderDetailByIdForUser")
                ->with(1,1)->once()->andReturn(
                    $this->getData(now())[0]
                );
        });

        $orderService = $this->app->make(OrderService::class);
        $order = $orderService->GetUserOrderDetails(1,1);
        $this->assertInstanceOf(OrderEntity::class,$order);
        $this->assertEquals(1,$order->id);
    }

    /**
     * Test order not found with id for user
     */
    public function test_invalid_order_for_user()
    {
        $this->mock(IOrderRepository::class,function (MockInterface $mock){
            return $mock->shouldReceive("getOrderDetailByIdForUser")
                ->with(1,1)->once()->andReturnNull();
        });

        $orderService = $this->app->make(OrderService::class);
        $order = $orderService->GetUserOrderDetails(1,1);
        $this->assertNull($order);

    }

    /**
     * Test submit an order for user
     */
    public function test_submit_new_order_for_user()
    {
        $this->mock(IOrderRepository::class,function (MockInterface $mock){
            return $mock->shouldReceive("create")
                ->withArgs(function ($input){
                    return ($input instanceof OrderCreateInput) && $input->user == 1;
                })->once()
                ->andReturn(
                    new OrderEntity(
                        1,
                        1,
                        100,
                        100,
                        null,
                        OrderStatusesEnum::Created,
                        now(),
                        now()
                    )
                );
        });

        $orderService = $this->app->make(OrderService::class);

        $data = new SubmitOrderInput(
            1,
            [
                [
                    'product_id'=> 1,
                    'count'=> 1,
                    'price'=> 100,
                    'old_price'=> null
                ]
            ]
        );
        $order = $orderService->SubmitOrder($data);
        $this->assertNotNull($order);
        $this->assertInstanceOf(OrderEntity::class,$order);
        $this->assertEquals(1,$order->id);
        $this->assertEquals(1,$order->user_id);
        $this->assertEquals(OrderStatusesEnum::Created,$order->status);
        $this->assertEquals(100,$order->total_price);
        $this->assertEquals(100,$order->pay_price);
        $this->assertNull($order->discount_price);

    }


    /**
     * Test failed to submit new order
     */
    public function test_failed_to_submit_new_order()
    {
        $this->mock(IOrderRepository::class,function (MockInterface $mock){
            return $mock->shouldReceive("create")
                ->withArgs(function ($input){
                    return ($input instanceof OrderCreateInput) && $input->user == 1;
                })->once()
                ->andReturnNull();
        });

        $orderService = $this->app->make(OrderService::class);

        $data = new SubmitOrderInput(
            1,
            [
                [
                    'product_id'=> 1,
                    'count'=> 1,
                    'price'=> 100,
                    'old_price'=> null
                ]
            ]
        );
        $this->expectException(FailedToCreateException::class);
        $orderService->SubmitOrder($data);
    }

    /**
     * Test delete user's order successfully
     */
    public function test_delete_user_order_successfully()
    {
        $this->mock(IOrderRepository::class,function (MockInterface $mock){
            $mock->shouldReceive("getOrderDetailByIdForUser")
                ->withAnyArgs()
                ->andReturn($this->getData(now())[0]);
            return $mock->shouldReceive("delete")
                ->with(1)->once()
                ->andReturnTrue();
        });

        $orderService = $this->app->make(OrderService::class);
        $result = $orderService->DeleteOrderForUser(1,1);
        $this->assertTrue($result);
    }

    /**
     * Test invalid user's order in delete
     */
    public function test_invalid_user_order()
    {
        $this->mock(IOrderRepository::class,function (MockInterface $mock){
            $mock->shouldReceive("getOrderDetailByIdForUser")
                ->withAnyArgs()
                ->andReturnNull();
        });

        $orderService = $this->app->make(OrderService::class);
        $this->expectException(OrderNotFoundException::class);
        $result = $orderService->DeleteOrderForUser(1,1);
    }

}
