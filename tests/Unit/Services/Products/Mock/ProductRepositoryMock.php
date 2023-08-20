<?php

namespace Tests\Unit\Services\Products\Mock;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Services\Products\Entities\ProductChangeEntity;
use App\Services\Products\Entities\ProductChangeInput;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use App\Services\Products\Interfaces\IProductRepository;
use Illuminate\Support\Collection;

class ProductRepositoryMock implements IProductRepository
{
    protected Collection $products;
    public array $changes = [];

    public function __construct(
        array $data = []
    )
    {
        $this->products = collect($data);
    }

    /**
     * @inheritDoc
     */
    public function getProductListWithPaginate(ProductFilterInput $filter): CustomSimplePaginate
    {
        $products = $this->products->map(function ($item){
            return new ProductEntity(
                $item["id"],
                $item["name"],
                $item["old_price"],
                $item["price"],
                $item["quantity"],
                $item["status"],
                $item["image"],
                $item["created_at"],
                $item["updated_at"]
            );
        });
        if ($filter->name)
            $products = $this->products->where("name","LIKE","%$filter->name%");
        $total      = $products->count();
        $paginate   = $products->forPage($filter->page,$filter->per_page);
        return New CustomSimplePaginate($paginate->collect(),$filter->per_page,$filter->page,($total - $filter->per_page > 0) ? 2 : null);
    }

    /**
     * @inheritDoc
     */
    public function getProductDetailById(int $id): ?ProductEntity
    {
        $product = $this->products->where("id","=",$id)->first();
        if ($product)
            return new ProductEntity(
                $product["id"],
                $product["name"],
                $product["old_price"],
                $product["price"],
                $product["quantity"],
                $product["status"],
                $product["image"],
                $product["created_at"],
                $product["updated_at"]
            );

        return null;
    }

    /**
     * @inheritDoc
     */
    public function addChangeToProduct(ProductChangeEntity $productChange): bool
    {
        $product = $this->products->where("id","=",$productChange->product_id)->first();
        if (!$product)
            throw new ProductNotFoundException();
        $this->changes[] = $productChange;

        return true;
    }

    public function getProductsWithIds(array $ids): Collection
    {
        // TODO: Implement getProductsWithIds() method.
    }
}
