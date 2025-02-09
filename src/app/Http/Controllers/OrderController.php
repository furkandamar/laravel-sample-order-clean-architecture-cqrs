<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Infrastructure\Handlers\Commands\CreateOrderCommand;
use App\Infrastructure\Handlers\HandlerBus;
use Illuminate\Http\Request;
use App\Infrastructure\Handlers\Commands\CancelOrderCommand;
use App\Infrastructure\Handlers\Commands\UpdateOrderCommand;
use App\Infrastructure\Handlers\Queries\GetDiscountQuery;
use App\Infrastructure\Handlers\Queries\GetOrderHistoryQuery;

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
     *         description="Sipariş başarıyla oluşturuldu"
     *     ),
     *      security={{"bearerAuth":{}}}
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
     *     summary="Sipariş paket veya sipariş içeriklerini döner",
     *     tags={"Orders"},
     *      @OA\Parameter(
     *         name="orderPackageId",
     *         in="path",
     *         description="Order Package UUID",
     *         required=false,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş listesi hazırlandı"
     *     ),
     *       security={{"bearerAuth":{}}}
     * )
     */
    public function getOrders(Request $request, $orderPackageId = null)
    {
        $query = new GetOrderHistoryQuery($request->attributes->get('customer')->id, $orderPackageId);
        $data = $this->handlerBus->handle($query);
        return ApiResponse::success($data);
    }

    /**
     * @OA\Get(
     *     path="/discount/{orderPackageId}",
     *     summary="İndirim listesini döner",
     *     tags={"Orders"},
     *      @OA\Parameter(
     *         name="orderPackageId",
     *         in="path",
     *         description="Order Package UUID",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş listesi hazırlandı"
     *     ),
     *       security={{"bearerAuth":{}}}
     * )
     */
    public function getDiscount(Request $request, $orderPackageId)
    {
        $data = $this->handlerBus->handle(new GetDiscountQuery($request->attributes->get('customer')->id, $orderPackageId));
        return ApiResponse::success($data);
    }

    /**
     * @OA\Put(
     *     path="/orders/{orderPackageId}",
     *     summary="Siparişi günceller",
     *     tags={"Orders"},
     *      @OA\Parameter(
     *         name="orderPackageId",
     *         in="path",
     *         description="Order Package UUID",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş paketi güncellendi"
     *     ),
     *       security={{"bearerAuth":{}}}
     * )
     */
    public function updateOrderPackage(Request $request, $orderPackageId)
    {
        $data = $this->handlerBus->handle(new UpdateOrderCommand($request->attributes->get('customer')->id, $orderPackageId, $request->all()));
        return ApiResponse::success($data);
    }

    /**
     * @OA\Delete(
     *     path="/orders/{orderPackageId}",
     *     summary="Siparişi iptal eder",
     *     tags={"Orders"},
     *      @OA\Parameter(
     *         name="orderPackageId",
     *         in="path",
     *         description="Order Package UUID",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş paketi silindi"
     *     ),
     *       security={{"bearerAuth":{}}}
     * )
     */
    public function cancelOrderPackage(Request $request, $orderPackageId)
    {
        $data = $this->handlerBus->handle(new CancelOrderCommand($request->attributes->get('customer')->id, $orderPackageId));
        return ApiResponse::success($data);
    }
}
