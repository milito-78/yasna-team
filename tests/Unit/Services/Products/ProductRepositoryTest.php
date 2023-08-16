<?php

namespace Tests\Unit\Services\Products;

use App\Infrastructure\Paginator\CustomSimplePaginate;
use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use App\Models\Product;
use App\Models\ProductChange;
use App\Services\Products\Entities\ProductChangeEntity;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use App\Services\Products\Repositories\ProductRepository;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;


class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private function seedDb(): void
    {
        $this->seed([
            ProductSeeder::class
        ]);
    }

    /**
     * Test get list of products
     */
    public function test_get_products_list_pagination(): void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        $repository = $this->app->make(ProductRepository::class);
        $products = $repository->getProductListWithPaginate(
            new ProductFilterInput(
                15,1
            )
        );

        $this->assertInstanceOf(CustomSimplePaginate::class,$products);
        $this->assertInstanceOf(Collection::class,$products->items());
        $this->assertEquals($products->perPage(),$products->items()->count());
        $this->assertEquals(2,$products->nextPage());
        $this->assertTrue($products->nextPageExist());
        $this->assertInstanceOf(ProductEntity::class,$products->items()->first());
        $this->assertTrue(
            $this->arrays_are_similar(
                array_keys(
                    $products->items()->first()->toArray()),
                    [
                        "id", "name", "old_price", "price",
                        "off_percentage", "quantity",
                        "status", "image",
                        "created_at", "updated_at"
                    ]
            )
        );
    }

    /**
     * Test get second page of products
     */
    public function test_get_next_page():void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        $repository = $this->app->make(ProductRepository::class);
        $products = $repository->getProductListWithPaginate(
            new ProductFilterInput(
                15,2
            )
        );

        $this->assertInstanceOf(CustomSimplePaginate::class,$products);
        $this->assertInstanceOf(Collection::class,$products->items());
        $this->assertEquals(5,$products->items()->count());
        $this->assertNull($products->nextPage());
        $this->assertFalse($products->nextPageExist());
    }

    /**
     * Test filter by name
     */
    public function test_filter_list_by_name():void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        /**
         * @var Product $special
         */
        $special = Product::query()->inRandomOrder()->first();

        $repository = $this->app->make(ProductRepository::class);
        $products = $repository->getProductListWithPaginate(
            new ProductFilterInput(
                15,1, $special->name
            )
        );

        $this->assertInstanceOf(CustomSimplePaginate::class,$products);
        $this->assertInstanceOf(Collection::class,$products->items());
        $found = $products->items()->where("id",$special->id)->first();
        $this->assertNotNull($found);
        $this->assertEquals($found->id,$special->id);
    }


    /**
     * Test Get a product by id
     */
    public function test_get_a_product_by_id_successfully():void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        /**
         * @var Product $product
         */
        $product    = Product::query()->inRandomOrder()->first();

        $repository = $this->app->make(ProductRepository::class);
        $found      = $repository->getProductDetailById($product->id);
        $this->assertNotNull($found);
        $this->assertInstanceOf(ProductEntity::class,$found);
        $this->assertEquals($product->toEntity(),$found);
    }

    /**
     * Test Cannot found a product by id
     */
    public function test_failed_to_found_by_id():void
    {
        $repository = $this->app->make(ProductRepository::class);
        $found      = $repository->getProductDetailById(0);
        $this->assertNull($found);
    }

    /**
     * Test Change product
     */
    public function test_add_changes_to_product_successfully()
    {
        /**
         * Seed database
         */
        $this->seedDb();

        /**
         * @var Product $product
         */
        $product    = Product::query()->inRandomOrder()->first();
        $beforeCount = $product->quantity;

        $repository = $this->app->make(ProductRepository::class);
        $change = new ProductChangeEntity(
            $product->id,
            1,
            ProductChangeReasonsEnum::System,
            ProductChangeTypesEnum::DecreaseVolume,
            ProductChangeStatusesEnum::Locked,
        );

        $result = $repository->addChangeToProduct($change);
        $this->assertTrue($result);
        $this->assertDatabaseCount(ProductChange::query()->where("product_id",$product->id),2);
        $product->refresh();
        $this->assertEquals($beforeCount,$product->quantity + 1);
    }

    /**
     * Test throw exception change product
     */
    public function test_add_changes_to_product_failed()
    {
        $repository = $this->app->make(ProductRepository::class);
        $change = new ProductChangeEntity(
            0,
            1,
            ProductChangeReasonsEnum::System,
            ProductChangeTypesEnum::DecreaseVolume,
            ProductChangeStatusesEnum::Locked,
        );

        $this->expectException(ProductNotFoundException::class);
        $repository->addChangeToProduct($change);
    }


    private function arrays_are_similar(array $array, array $similar): bool
    {

        if (count(array_diff_assoc($array, $similar))) {
            return false;
        }

        foreach($array as $k => $v) {
            if ($v !== $similar[$k]) {
                return false;
            }
        }

        return true;
    }
}
