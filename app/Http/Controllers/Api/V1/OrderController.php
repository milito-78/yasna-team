<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Orders\CreateRequest;
use App\Http\Requests\V1\Orders\IndexRequest;
use App\Http\Resources\Orders\OrderItemResourceCollection;
use App\Http\Resources\Orders\OrderResource;
use App\Http\Resources\Orders\OrderResourceCollection;
use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Order;
use App\Services\Orders\Entities\OrderFilterInput;
use App\Services\Orders\Entities\OrderItemEntity;
use App\Services\Orders\Entities\SubmitOrderInput;
use App\Services\Orders\Interfaces\IOrderService;
use App\Services\Products\Entities\ProductEntity;
use App\Services\Products\Entities\ProductsChangeInput;
use App\Services\Products\Exceptions\ProductNotFoundException;
use App\Services\Products\Interfaces\IProductService;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private readonly IOrderService $orderService,
        private readonly IProductService $productService,
    )
    {
    }

    public function index(IndexRequest $request)
    {
        $user = auth()->user();
        $filter = new OrderFilterInput(
            $request->validated('per_page',15),
            $request->validated('page',1),
            $request->validated('date'),
            $request->validated('status'),
        );

        $orders = simplePaginator($this->orderService->GetUserOrders($user->id,$filter));
        $orders->appends($request->query());

        return success_json()
            ->succeeded()
            ->message("Success")
            ->data(
                new OrderResourceCollection($orders)
            )
            ->send();
    }

    public function show($order)
    {
        $user = auth()->user();
        $tmp = $this->orderService->GetUserOrderDetails($user->id,$order);
        if (!$tmp)
            abort(404);

        $products_id = $tmp->items->pluck("product_id")->values()->toArray();
        $products = $this->productService->GetProductsInId($products_id);

        $tmp->items->each(function (OrderItemEntity &$order)use($products){
            $order->setProduct($products->where("id",$order->product_id)->first());
        });

        return success_json()
            ->succeeded()
            ->message("Success")
            ->data(
                (new OrderResource($tmp))->addItems(new OrderItemResourceCollection($tmp->items))
            )
            ->send();
    }

    public function store(CreateRequest $request)
    {
        $user = auth()->user();
        $items = collect($request->validated("items"));

        $products = $this->productService->GetProductsInId($items->pluck("id")->toArray());
        if ($products->count()!= $items->count())
            throw ValidationException::withMessages([
                "items" => ["product id is invalid"]
            ]);

        $items = $items->map(function ($item)use($products){
            /**
             * @var ProductEntity $product
             */
            $product = $products->where("id" , $item["id"])->first();
            if ($product->quantity < $item["count"]){
                throw ValidationException::withMessages([
                    "items" => ["Not enough quantity for this product"]
                ]);
            }else{
                $item["product_id"] = $product->id;
                $item["price"]      = $product->price;
                $item["old_price"]  = $product->old_price;
            }
            return $item;
        });

        $create = new SubmitOrderInput(
            $user->id,
            $items->toArray()
        );

        try {
            $order = $this->orderService->SubmitOrder($create);
        }catch (Throwable $exception){
            logError($exception,"Error during create order in controller",["create" => $create,"exception" => $exception],"01");
            return failed_json()->code(500)->message("There is an error during create order.")->send();
        }

        try {
            $this->productService->LockProductsCountForReason(new ProductsChangeInput($request->validated("items"),ProductChangeReasonsEnum::Order,Order::class,$order->id));
        }
        catch (ProductNotFoundException|Throwable $exception){
            logError($exception,"Error during lock products in controller",["items" => $items,"exception" => $exception],"02");
            $this->orderService->DeleteOrderForUser($order->user_id,$order->id);
            if ($exception instanceof ProductNotFoundException)
                throw ValidationException::withMessages([
                    "items" => ["product id is invalid"]
                ]);
            return failed_json()->code(500)->message("There is an error during create order.")->send();
        }

        try {
            $purchase = $this->orderService->StartPayment($order,$request->validated("gateway"));
        }catch (Throwable $exception){
            logError($exception,"Error during start payment in controller",["exception" => $exception],"03");
            $this->orderService->DeleteOrderForUser($user->id,$order->id);
            $this->productService->UnlockProductsCountForReason(new ProductsChangeInput($request->validated("items"),ProductChangeReasonsEnum::Order,Order::class,$order->id));
            return failed_json()->code(500)->message("There is an error during create order.")->send();
        }

        return success_json()
            ->created()
            ->message("Order submitted successfully")
            ->data([
                "order" => new OrderResource($order),
                "redirect_to_gateway" => $purchase->redirect_path
            ])
            ->send();
    }
}
