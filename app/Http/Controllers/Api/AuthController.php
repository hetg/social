<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ConfirmUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function getSignUp(){
        return view('auth.signup');
    }

    public function getEmailConf($token){
        $conf = ConfirmUser::where('token', $token)->first();

        if(!$conf){
            return redirect()
                ->route('auth.signin')
                ->with('danger', 'Wrong confirmation token!');
        }

        $user = User::where('email', $conf->email);

        $user->update(['validate' => 1]);
        $conf->delete();

        return redirect()
            ->route('auth.signin')
            ->with('success', 'Profile activated!');
    }

    public function postSignUp(Request $request){
        $this->validate($request, [
            'email' => 'required|unique:users|unique:conf_users|email|max:255',
            'first_name' => 'required|alpha|max:20',
            'last_name' => 'required|alpha|max:20',
            'password' => 'required|min:6|max:32|confirmed',
            'password_confirmation' => 'required|min:6|max:32'
        ]);

        $user = User::create([
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'password' => bcrypt($request->input('password'))
        ]);

        //  Auth::login($user);

        $token = Str::random(32);
        $app_url = env('APP_URL', 'localhost');

        ConfirmUser::create([
            'email' => $request->input('email'),
            'token' => $token
        ]);

        Mail::send('auth.email_token',['token'=>$token, 'app_url'=>$app_url],function($u) use ($user)
        {
            $u->from('heals.network@gmail.com');
            $u->to($user->email);
            $u->subject('Confirm registration');
        });

        // $user->getInfo()->create();

        return redirect()
            ->route('auth.signin')
            ->with('success', 'Your account has been created. Please activate your account');
    }

    public function getSignIn(){
        return view('auth.signin');
    }

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="admin@admin.com"),
     *       @OA\Property(property="password", type="string", format="password", example="password")
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */
    public function login(Request $request, AuthService $authService){
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        return $authService->login($request);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Sign in",
     *     description="Login by email, password",
     *     operationId="authMe",
     *     tags={"Auth"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Wrong credentials response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *         )
     *     ))
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     */
    public function me()
    {
        return response()->json(['ads'=>11]);
    }

    public function getSignOut(){
        Cache::pull('user-is-online-'.Auth::user()->id);

        Auth::logout();
        return redirect()
            ->route('home')
            ->with('success', 'You are successful sign out.');
    }
}
