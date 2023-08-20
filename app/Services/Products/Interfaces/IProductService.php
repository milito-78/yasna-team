<?php

namespace App\Services\Products\Interfaces;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Services\Products\Entities\ProductChangeInput;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Entities\ProductsChangeInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use Illuminate\Support\Collection;
use Throwable;

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

    /**
     * Get products with their ids
     * @param array $ids
     * @return Collection
     */
    public function GetProductsInId(array $ids): Collection;

    /**
     * Lock products count for reason
     *
     * @param ProductsChangeInput $input
     * @throws ProductNotFoundException|Throwable
     */
    public function LockProductsCountForReason(ProductsChangeInput $input):void;

    /**
     * Unlock products count for reason
     *
     * @param ProductsChangeInput $input
     * @throws ProductNotFoundException|Throwable
     */
    public function UnlockProductsCountForReason(ProductsChangeInput $input):void;

    /**
     * Decrease products count for reason
     *
     * @param ProductsChangeInput $input
     * @throws ProductNotFoundException|Throwable
     */
    public function DecreaseProductsCountForReason(ProductsChangeInput $input):void;

    /**
     * Increase products count for reason
     *
     * @param ProductsChangeInput $input
     * @throws ProductNotFoundException|Throwable
     */
    public function IncreaseProductsCountForReason(ProductsChangeInput $input):void;

}
