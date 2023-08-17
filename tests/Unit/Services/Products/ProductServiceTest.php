<?php

namespace Tests\Unit\Services\Products;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use App\Models\Enums\ProductStatusesEnum;
use App\Services\Products\Entities\ProductChangeEntity;
use App\Services\Products\Entities\ProductChangeInput;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use App\Services\Products\Interfaces\IProductRepository;
use App\Services\Products\Services\ProductService;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Unit\Services\Products\Mock\ProductRepositoryMock;

class ProductServiceTest extends TestCase
{

    private function getData(Carbon $time):array{
        return [
            [
                "id" => 1,
                "name" => "Test Name",
                "old_price" => null,
                "price" => 100,
                "quantity" => 10,
                "status" => ProductStatusesEnum::Active,
                "image" => "path/toimage.png",
                "created_at" => $time,
                "updated_at" => $time,
            ],
            [
                "id" => 2,
                "name" => "Unknown name",
                "old_price" => 200,
                "price" => 100,
                "quantity" => 20,
                "status" => ProductStatusesEnum::Active,
                "image" => "path/toimage2.png",
                "created_at" => $time,
                "updated_at" => $time,
            ],
        ];
    }

    /**
     * Get product list without filter
     */
    public function test_get_products_list_successfully(): void
    {
        $time = now();
        $productService = new ProductService(new ProductRepositoryMock($this->getData($time)));
        $filter = new ProductFilterInput(10,1);
        $products = $productService->GetListWithFilter($filter);
        $this->assertInstanceOf(CustomSimplePaginate::class,$products);
        $this->assertInstanceOf(ProductEntity::class,$products->items()->first());
    }

    /**
     * Get product detail by id
     */
    public function test_get_product_detail(): void
    {
        $time = now();
        $productService = new ProductService(new ProductRepositoryMock($this->getData($time)));
        $product = $productService->GetById(1);
        $this->assertInstanceOf(ProductEntity::class,$product);
        $this->assertNotNull($product);
        $this->assertEquals(1,$product->id);
    }

    /**
     * Invalid product id
     */
    public function test_invalid_id_for_detail(): void
    {
        $time = now();
        $productService = new ProductService(new ProductRepositoryMock($this->getData($time)));
        $product = $productService->GetById(0);
        $this->assertNull($product);
        $this->assertNotInstanceOf(ProductEntity::class,$product);
    }

    /**
     * Lock product
     */
    public function test_lock_product(): void
    {
        $time = now();
        $repository = new ProductRepositoryMock($this->getData($time));

        $productService = new ProductService($repository);
        $input = new ProductChangeInput(
            1,
            1,
            ProductChangeReasonsEnum::System
        );

        $result = $productService->LockCountForReason($input);
        $this->assertTrue($result);
        /**
         * @var ProductChangeEntity $saved
         */
        $saved = $repository->changes[0];
        $this->assertEquals($saved->product_id,$input->product_id);
        $this->assertEquals($saved->count,$input->count);
        $this->assertEquals(ProductChangeTypesEnum::DecreaseVolume, $saved->type);
        $this->assertEquals(ProductChangeStatusesEnum::Locked, $saved->status);
    }

    /**
     * Unlock product
     */
    public function test_unlock_product(): void
    {
        $time = now();
        $repository = new ProductRepositoryMock($this->getData($time));

        $productService = new ProductService($repository);
        $input = new ProductChangeInput(
            1,
            1,
            ProductChangeReasonsEnum::System
        );
        $result = $productService->UnlockCountForReason($input);
        $this->assertTrue($result);
        /**
         * @var ProductChangeEntity $saved
         */
        $saved = $repository->changes[0];
        $this->assertEquals($saved->product_id,$input->product_id);
        $this->assertEquals($saved->count,$input->count);
        $this->assertEquals(ProductChangeTypesEnum::IncreaseVolume, $saved->type);
        $this->assertEquals(ProductChangeStatusesEnum::Unlocked, $saved->status);
    }

    /**
     * Increase product
     */
    public function test_increase_product(): void
    {
        $time = now();
        $repository = new ProductRepositoryMock($this->getData($time));

        $productService = new ProductService($repository);
        $input = new ProductChangeInput(
            1,
            1,
            ProductChangeReasonsEnum::System
        );
        $result = $productService->IncreaseCountForReason($input);
        $this->assertTrue($result);
        /**
         * @var ProductChangeEntity $saved
         */
        $saved = $repository->changes[0];
        $this->assertEquals($saved->product_id,$input->product_id);
        $this->assertEquals($saved->count,$input->count);
        $this->assertEquals(ProductChangeTypesEnum::IncreaseVolume, $saved->type);
        $this->assertEquals(ProductChangeStatusesEnum::Increase, $saved->status);
    }

    /**
     * Decrease product
     */
    public function test_decrease_product(): void
    {
        $time = now();
        $repository = new ProductRepositoryMock($this->getData($time));

        $productService = new ProductService($repository);
        $input = new ProductChangeInput(
            1,
            1,
            ProductChangeReasonsEnum::System
        );
        $result = $productService->DecreaseCountForReason($input);
        $this->assertTrue($result);
        /**
         * @var ProductChangeEntity $saved
         */
        $saved = $repository->changes[0];
        $this->assertEquals($saved->product_id,$input->product_id);
        $this->assertEquals($saved->count,$input->count);
        $this->assertEquals(ProductChangeTypesEnum::DecreaseVolume, $saved->type);
        $this->assertEquals(ProductChangeStatusesEnum::Decrease, $saved->status);
    }

    /**
     * Invalid product in change product
     */
    public function test_invalid_id_for_change(): void
    {
        $time = now();
        $productService = new ProductService(new ProductRepositoryMock($this->getData($time)));
        $input = new ProductChangeInput(
            0,
            1,
            ProductChangeReasonsEnum::System
        );
        $this->expectException(ProductNotFoundException::class);
        $productService->UnlockCountForReason($input);
    }

    /**
     * Failed to add change for any reason
     */
    public function test_failed_to_add_change(): void
    {
        $mock = $this->mock(IProductRepository::class,function (MockInterface $mock){
            return $mock->shouldReceive("addChangeToProduct")->withAnyArgs()->once()->andReturn(false);
        });

        $productService = new ProductService($mock);
        $input = new ProductChangeInput(
            1,
            1,
            ProductChangeReasonsEnum::System
        );

        $result = $productService->UnlockCountForReason($input);
        $this->assertNotTrue($result);
    }

}
