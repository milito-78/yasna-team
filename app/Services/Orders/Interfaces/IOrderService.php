<?php

namespace App\Services\Orders\Interfaces;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\StartPaymentResult;
use App\Services\Orders\Entities\SubmitOrderInput;
use App\Services\Orders\Entities\TransactionEntity;
use App\Services\Orders\Entities\TransactionUpdateInput;
use App\Services\Orders\Exceptions\FailedToCreateException;
use App\Services\Orders\Exceptions\InvalidGatewayException;
use App\Services\Orders\Exceptions\InvalidTransactionException;
use App\Services\Orders\Exceptions\OrderNotFoundException;

interface IOrderService
{
    /**
     * Get user orders
     *
     * @param int $user
     * @param OrderFilterInput $filter
     * @return CustomSimplePaginate
     */
    public function GetUserOrders(int $user,OrderFilterInput $filter):CustomSimplePaginate;

    /**
     * Get user order
     *
     * @param int $user
     * @param int $order
     * @return OrderEntity|null
     */
    public function GetUserOrderDetails(int $user, int $order): ?OrderEntity;

    /**
     * Submit new order for user
     *
     * @param SubmitOrderInput $data
     * @return OrderEntity
     * @throws FailedToCreateException
     */
    public function SubmitOrder(SubmitOrderInput $data): OrderEntity;

    /**
     * Delete order with id
     *
     * @param int $user
     * @param int $order
     * @return bool
     * @throws OrderNotFoundException
     */
    public function DeleteOrderForUser(int $user,int $order): bool;

    /**
     * Start payment for order
     *
     * @param OrderEntity $order
     * @param string $gateway
     * @return StartPaymentResult
     * @throws InvalidGatewayException|\Exception
     */
    public function StartPayment(OrderEntity $order, string $gateway) : StartPaymentResult;


    /**
     * Get transaction by uuid
     *
     * @param string $uuid
     * @return OrderEntity
     * @throws InvalidTransactionException
     */
    public function FindOrderAndTransaction(string $uuid):OrderEntity;

    /**
     * Update transaction
     *
     * @param TransactionUpdateInput $input
     * @return bool
     * @throws InvalidTransactionException
     */
    public function UpdateTransaction(TransactionUpdateInput $input):bool;
}
