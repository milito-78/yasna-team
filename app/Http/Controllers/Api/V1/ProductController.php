<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\IndexRequest;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Products\ProductResourceCollection;
use App\Models\Product;
use App\Services\Products\Entities\ProductFilterInput;
use App\Services\Products\Interfaces\IProductService;

class ProductController extends Controller
{
    public function __construct(
        private readonly IProductService $service
    )
    {
    }


    public function index(IndexRequest $request)
    {
        $per_page   = $request->validated('per_page',15);
        $page       = $request->validated('page',1);
        $filter = new ProductFilterInput(
            $per_page,
            $page,
            $request->validated('name'),
        );
        $products = simplePaginator($this->service->GetListWithFilter($filter));
        $products->appends($request->query());

        return success_json()
                ->succeeded()
                ->message("Success")
                ->data(
                    new ProductResourceCollection($products)
                )
                ->send();
    }

    public function show($product)
    {
        $tmp = $this->service->GetById($product);
        if (!$tmp)
            abort(404);

        return success_json()
            ->succeeded()
            ->message("Success")
            ->data(
                new ProductResource($tmp)
            )
            ->send();
    }
}
