<?php

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\JWTAuth;

class AuthService
{

    /**
     * @var JWTAuth
     */
    private $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get a JWT via given credentials
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! $token = $this->auth->attempt($request->only(['email','password']))){
            return response()->json(['message' => 'Sorry, wrong email address or password. Please try again'], 422);
        }

        /** @var User $user */
        $user = $this->auth->user();

        if($user->validate == 0){
            Auth::logout();
            return response()->json(['message' => 'Please activate your account'], 403);
        }

        return response()->json(['access_token' => $token]);
    }


    /**
     * Log the user out (Invalidate the token)
     *
     * @return JsonResponse
     */
    public function logout()
    {
        /** @var User $user */
        $user = $this->auth->user();

        Cache::pull('user-is-online-'.$user->id);
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return response()->json(['access_token' => $this->auth->parseToken()->refresh()]);
    }

}
