<?php

namespace App\Services\Orders\Interfaces;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\OrderStatusesEnum;
use App\Services\Orders\Entities\OrderCreateInput;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\TransactionCreateInput;
use App\Services\Orders\Entities\TransactionEntity;
use App\Services\Orders\Entities\TransactionUpdateInput;
use App\Services\Orders\Exceptions\InvalidTransactionException;
use App\Services\Orders\Exceptions\OrderNotFoundException;

interface IOrderRepository
{
    /**
     * Create new order with order items
     *
     * @param OrderCreateInput $data
     * @return OrderEntity|null
     */
    public function create(OrderCreateInput $data) : ?OrderEntity;

    /**
     * Change order status by id
     *
     * @param int $order
     * @param OrderStatusesEnum $status
     * @return bool
     */
    public function changeStatus(int $order,OrderStatusesEnum $status): bool;

    /**
     * Delete order by id
     *
     * @param int $order
     * @return bool
     */
    public function delete(int $order) : bool;

    /**
     * Get list of orders for user with simple paginate (Without total count)
     *
     * @param int $user
     * @param OrderFilterInput $filter
     * @return CustomSimplePaginate
     */
    public function getOrderListForUserWithPaginate(int $user,OrderFilterInput $filter): CustomSimplePaginate;

    /**
     * Get order details with id for user
     *
     * @param int $user
     * @param int $id
     * @return OrderEntity|null
     */
    public function getOrderDetailByIdForUser(int $user,int $id): ?OrderEntity;

    /**
     * Create a transaction for order
     *
     * @param TransactionCreateInput $data
     * @return TransactionEntity|null
     * @throws OrderNotFoundException
     */
    public function createTransaction(TransactionCreateInput $data): ?TransactionEntity;

    /**
     * Get order by transaction uuid
     *
     * @param string $uuid
     * @return OrderEntity
     * @throws InvalidTransactionException
     */
    public function getOrderByTransactionUUId(string $uuid) : OrderEntity;

    /**
     * Update transaction by UUid
     *
     * @param TransactionUpdateInput $data
     * @return bool
     * @throws InvalidTransactionException
     */
    public function updateTransactionByUUid(TransactionUpdateInput $data) : bool;
}
