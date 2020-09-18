<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function getNotifications(){
        $user = Auth::user();

        return response()->json($user->notifications);
    }

}
