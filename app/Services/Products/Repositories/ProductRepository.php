<?php

namespace App\Services\Products\Repositories;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Product;
use App\Services\Products\Entities\ProductChangeEntity;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use App\Services\Products\Interfaces\IProductRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository implements IProductRepository
{

    private function query(): Builder
    {
        return Product::query();
    }

    /**
     * @inheritDoc
     */
    public function getProductListWithPaginate(ProductFilterInput $filter): CustomSimplePaginate
    {
        $products = $this->query()
                    ->filter(array_filter($filter->toArray(),fn($value) => !is_null($value)))
                    ->latest("id")
                    ->simplePaginate(perPage: $filter->per_page,page: $filter->page);
        return $this->convertToSimplePaginate($products);
    }

    /**
     * @inheritDoc
     */
    public function getProductDetailById(int $id): ?ProductEntity
    {
        /**
         * @var Product $product
         */
        $product = $this->query()->activeProduct()->where("id",$id)->first();

        return $product?->toEntity();
    }

    /**
     * @inheritDoc
     */
    public function addChangeToProduct(ProductChangeEntity $productChange): bool
    {
        /**
         * @var Product $product
         */
        $product = $this->query()->activeProduct()->where("id",$productChange->product_id)->first();
        if (!$product)
            throw new ProductNotFoundException();

        try {
            DB::statement("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ");
            $product->changes()->create([
                "product_id"        => $productChange->product_id,
                "count"             => $productChange->count,
                "reason_id"         => $productChange->reason->value,
                "reasonable_type"   => $productChange->reasonable_type,
                "reasonable_id"     => $productChange->reasonable_id,
                "type"              => $productChange->type,
                "status"            => $productChange->status,
            ]);
            return true;
        }catch (\Throwable $exception){
            logError($exception,"Error during create new change for product",$productChange->toArray());
        }
        return false;
    }


    /**
     * Change Paginator to custom paginator
     *
     * @param Paginator $paginator
     * @return CustomSimplePaginate
     */
    private function convertToSimplePaginate(Paginator $paginator):CustomSimplePaginate{
        return new CustomSimplePaginate(
            $this->wrapWithEntities($paginator->items()),
            $paginator->perPage(),
            $paginator->currentPage(),
            $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null
        );
    }

    /**
     * Wrap array of models to collection of entities
     *
     * @param array $items
     * @return Collection<ProductEntity>
     */
    public function wrapWithEntities(array $items): Collection
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
     * @param Product $user
     * @return ProductEntity
     */
    private function wrapWithEntity(Product $user): ProductEntity
    {
        return $user->toEntity();
    }
}
