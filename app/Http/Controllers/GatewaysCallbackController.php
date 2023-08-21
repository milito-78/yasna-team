<?php

namespace App\Http\Controllers;

use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Order;
use App\Services\Orders\Entities\OrderItemEntity;
use App\Services\Orders\Interfaces\IOrderService;
use App\Services\Products\Entities\ProductsChangeInput;
use App\Services\Products\Interfaces\IProductService;
use Illuminate\Http\Request;

class GatewaysCallbackController extends Controller
{
    public function __construct(
        private readonly IProductService $productService,
        private readonly IOrderService   $orderService,
    )
    {
    }

    public function callback(Request $request,$gateway)
    {
        $result = $this->orderService->ValidateCallback($gateway,$request->all());
        if ($result->status){
            $product_ids =  $result->order->items->map(function (OrderItemEntity $item){
                return [
                    "id" => $item->product_id,
                    "count" => $item->count
                ];
            })->toArray();
            $this->productService->UnlockProductsCountForReason(
                new ProductsChangeInput($product_ids,ProductChangeReasonsEnum::GatewayCallback,Order::class,$result->order->id)
            );
            $this->productService->DecreaseProductsCountForReason(
                new ProductsChangeInput($product_ids,ProductChangeReasonsEnum::GatewayCallback,Order::class,$result->order->id)
            );
            return success_json()->code(200)->message("successfully paid")->send();
        }

        if ($result->order){
            $product_ids =  $result->order->items->map(function (OrderItemEntity $item){
                return [
                    "id" => $item->product_id,
                    "count" => $item->count
                ];
            })->toArray();
            $this->productService->UnlockProductsCountForReason(
                new ProductsChangeInput($product_ids,ProductChangeReasonsEnum::GatewayCallback,Order::class,$result->order->id)
            );
            return failed_json()->code(400)->message("failed to paid")->send();
        }

        return success_json()->code(404)->message("invalid transaction")->send();
    }

}
