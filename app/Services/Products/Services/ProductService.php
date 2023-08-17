<?php

namespace App\Services\Products\Services;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use App\Services\Products\Entities\ProductChangeEntity;
use App\Services\Products\Entities\ProductChangeInput;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use App\Services\Products\Interfaces\IProductRepository;
use App\Services\Products\Interfaces\IProductService;

class ProductService implements IProductService
{
    public function __construct(
        private readonly IProductRepository $repository
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function GetListWithFilter(ProductFilterInput $filterInput): CustomSimplePaginate
    {
        return $this->repository->getProductListWithPaginate($filterInput);
    }

    /**
     * @inheritDoc
     */
    public function GetById(int $productId): ?ProductEntity
    {
        return $this->repository->getProductDetailById($productId);
    }

    /**
     * @inheritDoc
     */
    public function LockCountForReason(ProductChangeInput $input): bool
    {
        return $this->addChangeToProduct($input,ProductChangeStatusesEnum::Locked,ProductChangeTypesEnum::DecreaseVolume);
    }

    /**
     * @inheritDoc
     */
    public function UnlockCountForReason(ProductChangeInput $input): bool
    {

        return $this->addChangeToProduct($input,ProductChangeStatusesEnum::Unlocked,ProductChangeTypesEnum::IncreaseVolume);
    }

    /**
     * @inheritDoc
     */
    public function IncreaseCountForReason(ProductChangeInput $input): bool
    {
        return $this->addChangeToProduct($input,ProductChangeStatusesEnum::Increase,ProductChangeTypesEnum::IncreaseVolume);
    }

    /**
     * @inheritDoc
     */
    public function DecreaseCountForReason(ProductChangeInput $input): bool
    {
        return $this->addChangeToProduct($input,ProductChangeStatusesEnum::Decrease,ProductChangeTypesEnum::DecreaseVolume);
    }


    /**
     * Private add change to product
     *
     * @param ProductChangeInput $input
     * @param ProductChangeStatusesEnum $statusesEnum
     * @return bool
     * @throws ProductNotFoundException
     */
    private function addChangeToProduct(ProductChangeInput $input,ProductChangeStatusesEnum $statusesEnum,ProductChangeTypesEnum $typesEnum):bool
    {
        $data = new ProductChangeEntity(
            $input->product_id,
            $input->count,
            $input->reason,
            $typesEnum,
            $statusesEnum,
            $input->reasonable_type,
            $input->reasonable_id
        );
        return $this->repository->addChangeToProduct($data);
    }
}
