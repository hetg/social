<?php

namespace App\Services;

use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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


    /**
     * Update user's info
     *
     * @param int $userId
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function updateUser(int $userId, UserUpdateRequest $request)
    {
        /** @var User $user */
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        if ($user->id !== Auth::user()->id && !Auth::user()->admin){
            abort(403);
        }

        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'location' => $request->input('location'),
        ]);

        return response()->json($user);
    }

    /**
     * @param int $userId
     * @param UserUpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updateUserPassword(int $userId, UserUpdatePasswordRequest $request)
    {
        /** @var User $user */
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        if ($user->id !== Auth::user()->id && !Auth::user()->admin){
            abort(403);
        }

        Auth::user()->update([
            'password' => bcrypt($request->input('password'))
        ]);

        return response()->json($user);
    }

}
