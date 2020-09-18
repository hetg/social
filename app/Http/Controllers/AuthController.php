<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ConfirmUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

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

        $token = str_random(32);
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

    public function postSignIn(Request $request){
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only(['email','password']), $request->has('remember'))){
            return redirect()
                ->back()
                ->with('danger', 'Could not sign you in with those details.');
        }

        $user = Auth::user();

        if($user->validate == 0){
            Auth::logout();
            return redirect()
                ->route('auth.signin')
                ->with('info', 'Please activate your account.');
        }

        return redirect()
            ->route('home')
            ->with('success', 'You are now signed in.');
    }

    public function getSignOut(){
        Cache::pull('user-is-online-'.Auth::user()->id);

        Auth::logout();
        return redirect()
            ->route('home')
            ->with('success', 'You are successful sign out.');
    }
}
