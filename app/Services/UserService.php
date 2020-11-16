<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserService
{

    /**
     * Get user
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getUser(int $userId)
    {
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        return response()->json($user);
    }

}
