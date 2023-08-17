<?php

namespace App\Services\Products\Interfaces;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Services\Products\Entities\ProductChangeInput;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Exceptions\ProductNotFoundException;

interface IProductService
{
    /**
     * Get Products with filter
     *
     * @param ProductFilterInput $filterInput
     * @return CustomSimplePaginate
     */
    public function GetListWithFilter(ProductFilterInput $filterInput): CustomSimplePaginate;

    /**
     * Get product by id
     *
     * @param int $productId
     * @return ProductEntity|null
     */
    public function GetById(int $productId) : ?ProductEntity;

    /**
     * Lock product count for reason
     *
     * @param ProductChangeInput $input
     * @return bool
     * @throws ProductNotFoundException
     */
    public function LockCountForReason(ProductChangeInput $input) : bool;

    /**
     * Unlock product count for reason
     *
     * @param ProductChangeInput $input
     * @return bool
     * @throws ProductNotFoundException
     */
    public function UnlockCountForReason(ProductChangeInput $input) : bool;

    /**
     * Increase product count for reason
     *
     * @param ProductChangeInput $input
     * @return bool
     * @throws ProductNotFoundException
     */
    public function IncreaseCountForReason(ProductChangeInput $input) : bool;

    /**
     * Decrease product count for reason
     *
     * @param ProductChangeInput $input
     * @return bool
     * @throws ProductNotFoundException
     */
    public function DecreaseCountForReason(ProductChangeInput $input) : bool;
}
