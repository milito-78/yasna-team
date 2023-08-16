<?php

namespace App\Services\Products\Interfaces;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Services\Products\Entities\ProductChangeEntity;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Exceptions\ProductNotFoundException;

interface IProductRepository
{

    /**
     * Get list of product with simple paginate (Without total count)
     *
     * @param ProductFilterInput $filter
     * @return CustomSimplePaginate
     */
    public function getProductListWithPaginate(ProductFilterInput $filter): CustomSimplePaginate;

    /**
     * Get product detail by id
     *
     * @param int $id
     * @return ProductEntity|null
     */
    public function getProductDetailById(int $id): ?ProductEntity;

    /**
     * Add new change to product (Increase/Decrease count of product)
     *
     * @param ProductChangeEntity $productChange
     * @return bool
     * @throws ProductNotFoundException
     */
    public function addChangeToProduct(ProductChangeEntity $productChange): bool;

}
