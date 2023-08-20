<?php

namespace App\Services\Orders\Services;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Infrastructure\Payments\Factory;
use App\Infrastructure\Payments\Models\Invoice;
use App\Models\Enums\PaymentGatewayEnum;
use App\Services\Orders\Entities\OrderCreateInput;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\OrderItemCreateInput;
use App\Services\Orders\Entities\StartPaymentResult;
use App\Services\Orders\Entities\SubmitOrderInput;
use App\Services\Orders\Entities\TransactionCreateInput;
use App\Services\Orders\Entities\TransactionEntity;
use App\Services\Orders\Entities\TransactionUpdateInput;
use App\Services\Orders\Exceptions\FailedToCreateException;
use App\Services\Orders\Exceptions\InvalidGatewayException;
use App\Services\Orders\Exceptions\InvalidTransactionException;
use App\Services\Orders\Exceptions\OrderNotFoundException;
use App\Services\Orders\Interfaces\IOrderRepository;
use App\Services\Orders\Interfaces\IOrderService;
use Illuminate\Support\Facades\DB;

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
            $price += $product["price"] * $product["count"];
            $total_price += (is_null($product["old_price"]) ? $product["price"]:$product["old_price"]) * $product["count"];

            return (new OrderItemCreateInput(
                $product["product_id"],
                $product["price"],
                $product["old_price"],
                $product["count"],
            ))->toArray();
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

    /**
     * @inheritdoc
     */
    public function StartPayment(OrderEntity $order, string $gateway): StartPaymentResult
    {
        $gate = PaymentGatewayEnum::getFromName($gateway);
        if (!$gate || !$paymentDriverEnum = $gate->toGatewayEnums())
            throw new InvalidGatewayException();

        $paymentDriver = (new Factory())->getPaymentMethod($paymentDriverEnum);

        $invoice = new Invoice(
            $order->pay_price,null,null
        );

        $result = $paymentDriver->startPayment($invoice);
        if (!$result->isSuccess())
            throw new \Exception();

        $transaction = $this->repository->createTransaction(new TransactionCreateInput(
                $order->id,
                $invoice->getUuid(),
                $order->pay_price,
                $gate
            ));

        return new StartPaymentResult($transaction,$result->getRedirectPath(),$result->isSuccess());
    }

    /**
     * @inheritdoc
     */
    public function FindOrderAndTransaction(string $uuid): OrderEntity
    {
        return $this->repository->getOrderByTransactionUUId($uuid);
    }

    /**
     * @inheritdoc
     */
    public function UpdateTransaction(TransactionUpdateInput $input): bool
    {
        return $this->repository->updateTransactionByUUid($input);
    }
}
