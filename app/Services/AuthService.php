<?php

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ConfirmUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        return response()->json(
            [
                'access_token' => $token,
                'user_id' => $user->id
            ]
        );
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'password' => bcrypt($request->input('password'))
        ]);

        $token = Str::random(32);
        $app_url = env('APP_URL', 'localhost');

        $confirmUser = ConfirmUser::create([
            'email' => $request->input('email'),
            'token' => $token
        ]);

        try {
            Mail::send('auth.email_token', ['token' => $token, 'app_url' => $app_url], function ($u) use ($user) {
                $u->from('heals.network@gmail.com');
                $u->to($user->email);
                $u->subject('Confirm registration');
            });
        }catch (\Exception $exception){
            $user->delete();
            $confirmUser->delete();
            return response()->json(['message' => 'Something wrong with email sending, please try again later'], 500);
        }

        return response()->json(['user_id' => $user->id, 'app_url' => $app_url, 'token' => $token, 'confirm_url' => $app_url.'/signup/'.$token], 201);
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
