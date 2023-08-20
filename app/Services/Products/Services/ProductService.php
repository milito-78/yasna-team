<?php

namespace App\Services\Products\Services;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use App\Services\Products\Entities\ProductChangeEntity;
use App\Services\Products\Entities\ProductChangeInput;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Entities\ProductsChangeInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use App\Services\Products\Interfaces\IProductRepository;
use App\Services\Products\Interfaces\IProductService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

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
     * @param ProductChangeTypesEnum $typesEnum
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

    /**
     * @inheritdoc
     */
    public function GetProductsInId(array $ids): Collection
    {
        return $this->repository->getProductsWithIds($ids);
    }

    /**
     * @inheritdoc
     */
    public function LockProductsCountForReason(ProductsChangeInput $input):void
    {
        try {
            DB::beginTransaction();
            foreach ($input->products as $product){
                if(!$this->LockCountForReason(new ProductChangeInput($product["id"],$product["count"],$input->reason,$input->reasonable_type,$input->reasonable_id))){
                    throw new \Exception("can't lock product");
                }
            }
            DB::commit();
            return;
        }catch (Throwable $exception){
            DB::rollBack();
            if (! ($exception instanceof ProductNotFoundException))
                logError($exception,"Error during lock products");
            throw $exception;
        }
    }

    /**
     * @inheritdoc
     */
    public function UnlockProductsCountForReason(ProductsChangeInput $input):void
    {
        try {
            DB::beginTransaction();
            foreach ($input->products as $product){
                if(!$this->UnlockCountForReason(new ProductChangeInput($product["id"],$product["count"],$input->reason,$input->reasonable_type,$input->reasonable_id))){
                    throw new \Exception("can't unlock product");
                }
            }
            DB::commit();
            return;
        }catch (Throwable $exception){
            DB::rollBack();
            if (! ($exception instanceof ProductNotFoundException))
                logError($exception,"Error during unlock products");
            throw $exception;
        }
    }

    /**
     * @inheritdoc
     */
    public function DecreaseProductsCountForReason(ProductsChangeInput $input):void
    {
        try {
            DB::beginTransaction();
            foreach ($input->products as $product){
                if(!$this->DecreaseCountForReason(new ProductChangeInput($product["id"],$product["count"],$input->reason,$input->reasonable_type,$input->reasonable_id))){
                    throw new \Exception("can't decrease product");
                }
            }
            DB::commit();
            return;
        }catch (Throwable $exception){
            DB::rollBack();
            if (! ($exception instanceof ProductNotFoundException))
                logError($exception,"Error during decrease products");
            throw $exception;
        }
    }

    /**
     * @inheritdoc
     */
    public function IncreaseProductsCountForReason(ProductsChangeInput $input):void
    {
        try {
            DB::beginTransaction();
            foreach ($input->products as $product){
                if(!$this->IncreaseCountForReason(new ProductChangeInput($product["id"],$product["count"],$input->reason,$input->reasonable_type,$input->reasonable_id))){
                    throw new \Exception("can't increase product");
                }
            }
            DB::commit();
            return;
        }catch (Throwable $exception){
            DB::rollBack();
            if (! ($exception instanceof ProductNotFoundException))
                logError($exception,"Error during increase products");
            throw $exception;
        }
    }
}
