<?php

namespace App\Services;

use App\Http\Requests\User\UserUpdateAvatarRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Webpatser\Uuid\Uuid;

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
     * Get user friends
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getUserFriends(int $userId)
    {
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        return response()->json($user->friends());
    }

    /**
     * Get user friend requests
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getUserFriendRequests(int $userId)
    {
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        return response()->json($user->friendRequests());
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

    /**
     * @param int $userId
     * @param UserUpdateAvatarRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateUserAvatar(int $userId, UserUpdateAvatarRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        if ($user->id !== Auth::user()->id && !Auth::user()->admin){
            abort(403);
        }

        $avatar = $request->file('avatar');
        $filename = Uuid::generate() . '.' . $avatar->getClientOriginalExtension();

        if ($avatar->getClientOriginalExtension() == "jpg" || $avatar->getClientOriginalExtension() == "png" || $avatar->getClientOriginalExtension() == "jpeg") {
            if ($user->avatar !== 'default.png') {
                $file = public_path('storage/images/avatar/' . $user->avatar);
                $file_small = public_path('storage/images/avatar/smalls/' . $user->avatar);

                if (File::exists($file)) {
                    unlink($file);
                }

                if (File::exists($file_small)) {
                    unlink($file_small);
                }

            }

            $img = Image::make($avatar->getRealPath());
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });

            $img->stream(); // <-- Key point

            //dd();
            Storage::disk('public')->put('images/avatar/'.$filename, $img, 'public');

            $img->resize(120, 120, function ($constraint) {
                $constraint->aspectRatio();
            });

            $img->stream(); // <-- Key point

            Storage::disk('public')->put('images/avatar/smalls/'.$filename, $img, 'public');

            $user->update([
                'avatar' => $filename
            ]);

        } else {
            return response()->json('Wrong file format',400);
        }

        return response()->json($user);
    }

    /**
     * @param int $userId
     * @param int $friendId
     * @return JsonResponse
     */
    public function addFriend(int $userId, int $friendId)
    {
        $user = User::find($userId);
        $friend = User::find($friendId);

        if (!$user || !$friend){
            abort(404);
        }

        if ($userId === $friendId){
            abort(400);
        }

        if ($user->hasFriendRequestPending($friend) || $friend->hasFriendRequestPending($user)){
            return response()->json(['message' => 'Friend request already pending.']);
        }

        if ($user->isFriendsWith($friend)){
            return response()->json(['message' => 'You are already friends']);
        }

        $user->addFriend($friend);

        return response()->json(['message' => 'Friend request sent.']);
    }

    /**
     * @param int $userId
     * @param int $friendId
     * @return JsonResponse
     */
    public function acceptFriend(int $userId, int $friendId): JsonResponse
    {
        $user = User::find($userId);
        $friend = User::find($friendId);

        if (!$user || !$friend){
            abort(404);
        }

        if ($userId === $friendId){
            abort(400);
        }

        if (!$user->hasFriendRequestReceived($friend)){
            return response()->json(['message' => 'No friends request found'],404);
        }

        $user->acceptFriendRequest($friend);

        return response()->json(['message' => 'Friend request accepted']);
    }

    /**
     * @param int $userId
     * @param int $friendId
     * @return JsonResponse
     */
    public function deleteFriend(int $userId, int $friendId): JsonResponse
    {
        $user = User::find($userId);
        $friend = User::find($friendId);

        if (!$user || !$friend){
            abort(404);
        }

        if ($userId === $friendId){
            abort(400);
        }

        if (!$user->isFriendsWith($friend)){
            return response()->json(['message' => 'It is not your friend'],404);
        }

        $user->deleteFriend($friend);

        return response()->json(['message' => 'Friend deleted'], 204);
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function getUserPosts(int $userId)
    {
        /** @var User $user */
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        return response()->json($user->statuses());
    }
}
