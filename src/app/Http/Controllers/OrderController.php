<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Infrastructure\Abstraction\Service\IOrderService;
use App\Infrastructure\Handlers\Commands\CreateOrderCommand;
use App\Infrastructure\Handlers\Commands\CreateOrderCommandHandler;
use App\Infrastructure\Handlers\HandlerBus;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Http\Controllers\AuthorizeController as BaseController;
use App\Infrastructure\Handlers\Queries\GetDiscountQuery;
use App\Infrastructure\Handlers\Queries\GetOrderHistoryQuery;

/**
 * @OA\Info(
 *      title="Sipariş API",
 *      version="1.0.0",
 *      description="Sipariş API belgelendirmesi"
 * )
 *
 * @OA\Tag(
 *     name="Orders",
 *     description="Sipariş işlemleri"
 * )
 */
class OrderController extends Controller
{

    public function __construct(private HandlerBus $handlerBus) {}

    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Yeni sipariş oluştur",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            type="array",
     *           @OA\Items(
     *             required={"product_id", "amount"},
     *             @OA\Property(property="product_id", type="string"),
     *             @OA\Property(property="amount", type="number", format="decimal", example=0.00)
     * )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş başarıyla oluşturuldu",
     *         @OA\JsonContent(ref="/App/Models/OrderModel")
     *     )
     * )
     */
    public function createOrder(Request $request)
    {
        $command = new CreateOrderCommand($request->attributes->get('customer')->id, $request->all());
        $create = $this->handlerBus->handle($command);

        return ApiResponse::success($create);
    }

    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="Yeni sipariş oluştur",
     *     tags={"Orders"},
     *      @OA\Parameter(
     *         name="order_package_id",
     *         in="query",
     *         description="Order Package UUID",
     *         required=false,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş listesi hazırlandı",
     *         @OA\JsonContent(ref="/App/Models/OrderPackageModel")
     *     )
     * )
     */
    public function getOrders(Request $request)
    {
        $query = new GetOrderHistoryQuery($request->attributes->get('customer')->id, $request->input('order_package_id'));
        $data = $this->handlerBus->handle($query);
        return ApiResponse::success($data);
    }

    /**
     * @OA\Get(
     *     path="/discount",
     *     summary="İndirim listesini döner",
     *     tags={"Orders"},
     *      @OA\Parameter(
     *         name="order_package_id",
     *         in="path",
     *         description="Order Package UUID",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş listesi hazırlandı",
     *         @OA\JsonContent(ref="/App/Models/OrderDiscountModel")
     *     )
     * )
     */
    public function getDiscount($orderPackageId)
    {
        $data = $this->handlerBus->handle(new GetDiscountQuery($orderPackageId));
        return ApiResponse::success($data);
    }
}
