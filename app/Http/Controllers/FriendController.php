<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function getIndex(){
        $friends = Auth::user()->friends();
        $requests = Auth::user()->friendRequests();

        return view('friends.index')
            ->with('friends', $friends)
            ->with('requests', $requests);
    }

    public function getAdd($user_id){
        $user = User::where('id', $user_id)->first();

        if (!$user){
            return redirect()
                ->route('home')
                ->with('danger', 'That user could not be found.');
        }

        if (Auth::user()->id === $user->id){
            return redirect()->back();
        }

        if (Auth::user()->hasFriendRequestPending($user) || $user->hasFriendRequestPending(Auth::user())){
            return redirect()
                ->route('profile.index', ['user_id' => $user_id])
                ->with('info', 'Friend request already pending.');
        }

        if (Auth::user()->isFriendsWith($user)){
            return redirect()
                ->route('profile.index', ['user_id' => $user_id])
                ->with('info', 'You are already friends.');
        }

        Auth::user()->addFriend($user);

        return redirect()
            ->route('profile.index', ['user_id' => $user_id])
            ->with('succes', 'Friend request sent.');
    }

    public function getAccept($user_id){
        $user = User::where('id', $user_id)->first();

        if (!$user){
            return redirect()
                ->route('home')
                ->with('danger', 'That user could not be found.');
        }

        if (!Auth::user()->hasFriendRequestReceived($user)){
            return redirect()->route('home');
        }

        Auth::user()->acceptFriendRequest($user);

        return redirect()
            ->route('profile.index', ['user_id' => $user_id])
            ->with('success', 'Friend request accepted.');
    }

    public function postDelete($user_id){
        $user = User::where('id', $user_id)->first();

        if (!Auth::user()->isFriendsWith($user)){
            return redirect()
                ->route('profile.index', ['user_id' => $user_id])
                ->with('danger', 'It is not your friend.');
        }

        Auth::user()->deleteFriend($user);

        return redirect()
            ->route('profile.index', ['user_id' => $user_id])
            ->with('success', 'Friend deleted.');
    }
}
