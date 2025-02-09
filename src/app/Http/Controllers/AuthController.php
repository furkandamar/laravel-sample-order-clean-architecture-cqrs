<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Infrastructure\Handlers\Commands\GetMeQuery;
use App\Infrastructure\Handlers\Commands\LoginCommand;
use App\Infrastructure\Handlers\HandlerBus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    public function __construct(private HandlerBus $handlerBus) {}

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Oturum işlemleri",
     *     description="Oturum açma işlemi",
     *     operationId="login",
     *     tags={"Auth"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"email", "password"},
     *            @OA\Property(
     *              property="email",
     *              type="string",
     *              format="email",
     *              example="mail@example.com"
     *              ),
     *            @OA\Property(
     *              property="password",
     *              type="string",
     *              format="password",
     *              example="******"
     *              ),
     *         ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="\Illuminate\Http\JsonResponse"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function login(Request $request)
    {
        return ApiResponse::success(
            $this->handlerBus->handle(new LoginCommand($request->email, $request->password))
        );
    }

    /**
     * @OA\Get(
     *     path="/me",
     *     summary="Kullanıcı bilgisi",
     *     operationId="me",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             ref="\App\Models\User"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function me(Request $request)
    {
        return ApiResponse::success(
            $this->handlerBus->handle(new GetMeQuery($request->attributes->get('customer')->id))
        );
    }
}
