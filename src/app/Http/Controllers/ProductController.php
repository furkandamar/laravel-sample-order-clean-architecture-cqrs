<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Infrastructure\Handlers\HandlerBus;
use App\Infrastructure\Handlers\Queries\GetCategoryListQuery;
use App\Infrastructure\Handlers\Queries\GetProductListQuery;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function __construct(private HandlerBus $handlerBus) {}
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Kategoriler",
     *     description="Kategori listesini getirir",
     *     operationId="getCategories",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function getCategories()
    {
        $categories = $this->handlerBus->handle(new GetCategoryListQuery());
        return ApiResponse::success($categories);
    }

    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Ürün listesi",
     *     description="Returns a list of products",
     *     operationId="getProducts",
     *     tags={"Products"},
     *       @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Category UUID",
     *         required=false,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function getProducts(Request $request)
    {
        $products = $this->handlerBus->handle(new GetProductListQuery($request->input('category_id')));
        return ApiResponse::success($products);
    }
}
