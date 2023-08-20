<?php

namespace Tests\Unit\Services\Orders;


use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\OrderStatusesEnum;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Orders\Entities\OrderCreateInput;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\OrderItemCreateInput;
use App\Services\Orders\Repositories\OrderRepository;
use Database\Seeders\OrderSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private function seedDb(): void
    {
        $this->seed([
            UserSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }

    /**
     * Test get list of orders for user
     */
    public function test_get_user_orders_list_pagination(): void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        $user = User::query()->activeUser()->first();

        $repository = $this->app->make(OrderRepository::class);
        $orders = $repository->getOrderListForUserWithPaginate($user->id,
            new OrderFilterInput(
                15,1
            )
        );
        $this->assertInstanceOf(CustomSimplePaginate::class,$orders);
        $this->assertInstanceOf(Collection::class,$orders->items());
        $this->assertEquals($orders->perPage(),$orders->items()->count());
        $this->assertEquals(2,$orders->nextPage());
        $this->assertTrue($orders->nextPageExist());
        $this->assertInstanceOf(OrderEntity::class,$orders->items()->first());
        $this->assertTrue(
            $this->arrays_are_similar(
                array_keys(
                    $orders->items()->first()->toArray()),
                [
                    "id", "user_id", "total_price", "pay_price",
                    "discount_price", "status",
                    "created_at", "updated_at",
                    "items"
                ]
            )
        );
    }

    /**
     * Test get second page of orders
     */
    public function test_get_next_page():void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        $user = User::query()->activeUser()->first();

        $repository = $this->app->make(OrderRepository::class);
        $products = $repository->getOrderListForUserWithPaginate($user->id,
            new OrderFilterInput(
                15,2
            )
        );

        $this->assertInstanceOf(CustomSimplePaginate::class,$products);
        $this->assertInstanceOf(Collection::class,$products->items());
        $this->assertEquals(5,$products->items()->count());
        $this->assertNull($products->nextPage());
        $this->assertFalse($products->nextPageExist());
    }

    /**
     * Test Get an order by id for user
     */
    public function test_get_an_order_by_id_successfully():void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        /**
         * @var Order $order
         */
        $order    = Order::query()->inRandomOrder()->first();

        $repository = $this->app->make(OrderRepository::class);
        $found      = $repository->getOrderDetailByIdForUser($order->user_id,$order->id);
        $this->assertNotNull($found);
        $this->assertInstanceOf(OrderEntity::class,$found);
        $this->assertEquals($order->toEntity(),$found);
    }

    /**
     * Test Invalid user id
     */
    public function test_get_an_order_detail_invalid_user_id():void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        /**
         * @var Order $order
         */
        $order    = Order::query()->inRandomOrder()->first();

        $repository = $this->app->make(OrderRepository::class);
        $found      = $repository->getOrderDetailByIdForUser(0,$order->id);
        $this->assertNull($found);
    }

    /**
     * Test create an order for user
     * @return OrderEntity
     */
    public function test_create_order_for_user()
    {
        /**
         * Seed database
         */
        $this->seedDb();

        /**
         * New user
         *
         * @var User $user
         */
        $user = User::factory()->createOne();
        $price = 0;
        $total_price = 0;
        $discount_price = 0;

        $products = Product::query()
            ->inRandomOrder()
            ->limit(2)
            ->get()
            ->map(function (Product $product) use(&$price,&$discount_price,&$total_price){
                $price += $product->price;
                $total_price += is_null($product->old_price) ? $product->price:$product->old_price;
                $discount_price += is_null($product->old_price) ? 0 : $product->old_price - $product->price;
                return new OrderItemCreateInput(
                    $product->id,
                    $product->price,
                    $product->old_price,
                    $product->quantity,
                );
            })
            ->toArray();

        $repository = $this->app->make(OrderRepository::class);
        $order = $repository->create(
            new OrderCreateInput(
                $user->id,
                $total_price,
                $price,
                $discount_price,
                $products
            )
        );
        $this->assertNotNull($order);
        $this->assertInstanceOf(OrderEntity::class,$order);
        $this->assertEquals($user->id,$order->user_id);
        $this->assertDatabaseHas(Order::class,[
            "id"                => $order->id,
            "user_id"           => $user->id,
            "pay_price"         => $price,
            "discount_price"    => $discount_price,
            "total_price"       => $total_price,
            "status_id"         => OrderStatusesEnum::Created->value,
        ]);
        foreach ($products as $product){
            $this->assertDatabaseHas(OrderItem::class,[
                "order_id" => $order->id,
                "product_id" => $product["product_id"],
                "price" => $product["price"],
                "old_price" => $product["old_price"],
                "count" => $product["count"],
            ]);
        }

        $this->assertDatabaseCount(Order::query()->where("user_id",$user->id),1);

        return $order;
    }

    /**
     * Test change order status
     */
    public function test_change_order_status()
    {
        /**
         * Seed database
         */
        $this->seedDb();
        /**
         * @var Order $order
         */
        $order = Order::query()->inRandomOrder()->first();

        $repository = $this->app->make(OrderRepository::class);
        $result = $repository->changeStatus($order->id,OrderStatusesEnum::Delivered);
        $this->assertTrue($result);
        $this->assertDatabaseHas(Order::class,[
            "id" => $order->id,
            "status_id" => OrderStatusesEnum::Delivered->value
        ]);
    }

    /**
     * Test delete order
     */
    public function test_delete_order()
    {
        /**
         * Seed database
         */
        $this->seedDb();
        /**
         * @var Order $order
         */
        $order = Order::query()->inRandomOrder()->first();

        $repository = $this->app->make(OrderRepository::class);
        $result = $repository->delete($order->id);
        $this->assertTrue($result);
        $this->assertDatabaseMissing(Order::class,[
            "id" => $order->id
        ]);
    }

}
