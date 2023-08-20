<?php

namespace App\Services\Orders\Services;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Services\Orders\Entities\OrderCreateInput;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\OrderItemCreateInput;
use App\Services\Orders\Entities\SubmitOrderInput;
use App\Services\Orders\Exceptions\FailedToCreateException;
use App\Services\Orders\Exceptions\OrderNotFoundException;
use App\Services\Orders\Interfaces\IOrderRepository;
use App\Services\Orders\Interfaces\IOrderService;

class OrderService implements IOrderService
{
    public function __construct(
        private readonly IOrderRepository $repository
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function GetUserOrders(int $user, OrderFilterInput $filter): CustomSimplePaginate
    {
        return $this->repository->getOrderListForUserWithPaginate($user,$filter);
    }

    /**
     * @inheritdoc
     */
    public function GetUserOrderDetails(int $user,int $order): ?OrderEntity
    {
        return $this->repository->getOrderDetailByIdForUser($user,$order);
    }

    /**
     * @inheritdoc
     */
    public function SubmitOrder(SubmitOrderInput $data): OrderEntity
    {
        $total_price = 0;
        $price = 0;

        $itemsData = array_map(function ($product) use(&$price,&$total_price){
            $price += $product["price"];
            $total_price += is_null($product["old_price"]) ? $product["price"]:$product["old_price"];

            return new OrderItemCreateInput(
                $product["product_id"],
                $product["price"],
                $product["old_price"],
                $product["count"],
            );
        },$data->products);

        $createData = new OrderCreateInput(
            $data->user,
            $total_price,$price,
            $total_price - $price,
            $itemsData
        );

        $order = $this->repository->create($createData);
        if (!$order){
            throw new FailedToCreateException();
        }
        return $order;
    }

    /**
     * @inheritdoc
     */
    public function DeleteOrderForUser(int $user,int $order): bool
    {
        $order = $this->repository->getOrderDetailByIdForUser($user,$order);
        if (!$order)
            throw new OrderNotFoundException();

        return $this->repository->delete($order->id);
    }
}
