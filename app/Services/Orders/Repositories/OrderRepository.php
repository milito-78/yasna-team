<?php

namespace App\Services\Orders\Repositories;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\OrderStatusesEnum;
use App\Models\Enums\TransactionStatusEnum;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Orders\Entities\OrderCreateInput;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\TransactionCreateInput;
use App\Services\Orders\Entities\TransactionEntity;
use App\Services\Orders\Entities\TransactionUpdateInput;
use App\Services\Orders\Exceptions\InvalidTransactionException;
use App\Services\Orders\Exceptions\OrderNotFoundException;
use App\Services\Orders\Interfaces\IOrderRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository implements IOrderRepository
{

    private function query(): Builder
    {
        return Order::query();
    }

    private function queryTransaction(): Builder
    {
        return Transaction::query();
    }


    /**
     * @inheritdoc
     */
    public function create(OrderCreateInput $data): ?OrderEntity
    {
        $orderData = $data->toArray();
        $itemsData = $orderData["products"];
        unset($orderData["products"]);
        $orderData["status_id"] = OrderStatusesEnum::Created->value;

        try {
            DB::beginTransaction();

            /**
             * @var Order $order
             */
            $order = $this->query()->create($orderData);
            $order->items()->createMany($itemsData);
            DB::commit();
        }catch (\Throwable $exception){
            logError($exception,"Error during create order");
            DB::rollBack();
            return null;
        }

        return $this->wrapWithEntity($order);
    }

    /**
     * @inheritdoc
     */
    public function changeStatus(int $order, OrderStatusesEnum $status): bool
    {
        try {
            $this->query()->where("id",$order)->update(["status_id" => $status->value]);
        }catch (\Throwable $exception){
            logError($exception,"Error during update order status");
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete(int $order): bool
    {
        try {
            /**
             * @var Order $data
             */
            $data = $this->query()->where("id",$order)->first();
            $data->items()->delete();
            $this->query()->where("id",$order)->delete();
        }catch (\Throwable $exception){
            logError($exception,"Error during delete order");
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getOrderListForUserWithPaginate(int $user,OrderFilterInput $filter): CustomSimplePaginate
    {
        $orders = $this->query()
            ->userId($user)
            ->filter(array_filter($filter->toArray(),fn($value) => !is_null($value)))
            ->with("items")
            ->latest("id")
            ->simplePaginate(perPage: $filter->per_page,page: $filter->page);
        return $this->convertToSimplePaginate($orders);
    }

    /**
     * @inheritdoc
     */
    public function getOrderDetailByIdForUser(int $user,int $id): ?OrderEntity
    {
        /**
         * @var Order $order
         */
        $order = $this->query()->userId($user)->where("id",$id)->with("items")->first();

        if (!$order)
            return null;

        return $this->wrapWithEntity($order);
    }

    /**
     * @inheritdoc
     */
    public function createTransaction(TransactionCreateInput $data): ?TransactionEntity
    {
        /**
         * @var ?Order $order
         */
        $order = $this->query()->where("id",$data->order_id)->first();
        if (!$order)
            throw new OrderNotFoundException();

        $transaction = $this->queryTransaction()->create([
            "user_id" => $order->user_id,
            "price" => $data->price,
            "uuid" => $data->uuid,
            "payment_id" => $data->gateway->value,
            "transactionable_type" => Order::class,
            "transactionable_id" => $order->id,
            "status_id" => TransactionStatusEnum::Started->value
        ]);
        return $this->wrapWithTransactionEntity($transaction);
    }

    /**
     * @inheritdoc
     */
    public function getOrderByTransactionUUId(string $uuid): OrderEntity
    {
        /**
         * @var ?Order $order
         */
        $order = $this->query()
            ->whereHas("latestTransaction",function ($query) use ($uuid){
                return $query->where("uuid" , $uuid);
            })
            ->with("items")
            ->first();
        if (!$order)
            throw new InvalidTransactionException();

        return $this->wrapWithEntity($order);
    }

    /**
     * @inheritdoc
     */
    public function updateTransactionByUUid(TransactionUpdateInput $data): bool
    {
        $transaction = $this->queryTransaction()->where("uuid",$data->uuid)->first();
        if (!$transaction)
            throw new InvalidTransactionException();
        $transaction->update([
            "tracking_code" => $data->tracking_code,
            "status_id" => $data->status->value,
            "received_at" => now()
        ]);

        return true;
    }

    /**
     * Change Paginator to custom paginator
     *
     * @param Paginator $paginator
     * @return CustomSimplePaginate
     */
    private function convertToSimplePaginate(Paginator $paginator):CustomSimplePaginate{
        return customSimplePaginator(
            $this->wrapWithEntities($paginator->items()),
            $paginator->perPage(),
            $paginator->currentPage(),
            $paginator->hasMorePages()
        );
    }

    /**
     * Wrap array of models to collection of entities
     *
     * @param array $items
     * @return Collection<OrderEntity>
     */
    private function wrapWithEntities(array $items): Collection
    {
        $tmp = collect();
        foreach ($items as $item){
            $tmp->push($this->wrapWithEntity($item));
        }
        return $tmp;
    }

    /**
     * Convert model to entity
     *
     * @param Order $user
     * @return OrderEntity
     */
    private function wrapWithEntity(Order $user): OrderEntity
    {
        return $user->toEntity();
    }

    /**
     * Convert model to entity
     *
     * @param Transaction $user
     * @return TransactionEntity
     */
    private function wrapWithTransactionEntity(Transaction $user): TransactionEntity
    {
        return $user->toEntity();
    }
}
