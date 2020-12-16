<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\ConfirmUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register",
     *     description="Register by email, password",
     *     operationId="authRegister",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@admin.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *             @OA\Property(property="first_name", type="string", format="text", example="Admin"),
     *             @OA\Property(property="last_name", type="string", format="text", example="Admin")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Authorized response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Wrong credentials response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *         )
     *     )
     * )
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request){
        return $this->authService->register($request);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Log in",
     *     description="Log in by email, password",
     *     operationId="authLogin",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@admin.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authorized response",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="token"),
     *             @OA\Property(property="user_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Wrong credentials response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not activated response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Please activate your account")
     *         )
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request){
        return $this->authService->login($request);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout",
     *     description="Logout",
     *     operationId="authLogout",
     *     tags={"Auth"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @return JsonResponse
     */
    public function logout(){
        return $this->authService->logout();
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh JWT token",
     *     description="Refresh JWT token",
     *     operationId="authRefresh",
     *     tags={"Auth"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed response",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="token")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @return JsonResponse
     */
    public function refresh(){
        return $this->authService->refresh();
    }
}
