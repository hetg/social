<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function login(Request $request): JsonResponse
    {
        if (! $token = $this->auth->attempt($request->only(['email','password']))){
            return response()->json(['message' => 'Sorry, wrong email address or password. Please try again'], 422);
        }

        /** @var User $user */
        $user = $this->auth->user();

        if($user->validate == 0){
            Auth::logout();
            return response()->json(['message' => 'Please activate your account.'], 403);
        }

        return response()->json(['access_token' => $token]);
    }

}
