<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateAvatarRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Webpatser\Uuid\Uuid;

class UserController extends Controller
{

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/users/{query}",
     *     summary="Find users by name",
     *     description="Fins users by name",
     *     operationId="usersGet",
     *     tags={"User"},
     *     @OA\Parameter(
     *         description="Name of user",
     *         in="path",
     *         name="query",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="string",
     *             format="text"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     *
     * @param string $query
     * @return JsonResponse
     */
    public function find(string $query)
    {
        $users = User::where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$query}%")
            ->get();

        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{userId}",
     *     summary="Get user by ID",
     *     description="Get user by ID",
     *     operationId="userGet",
     *     tags={"User"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getUser(int $userId){
        return $this->userService->getUser($userId);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{userId}",
     *     summary="Update user by ID",
     *     description="Update user by ID",
     *     operationId="userPost",
     *     tags={"User"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User info",
     *         @OA\JsonContent(
     *             required={"first_name","last_name"},
     *             @OA\Property(property="first_name", type="string", format="text", example="Admin"),
     *             @OA\Property(property="last_name", type="string", format="text", example="Admin"),
     *             @OA\Property(property="location", type="string", format="text", example="Uzhhorod")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $userId
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function updateUser(int $userId, UserUpdateRequest $request){
        return $this->userService->updateUser($userId, $request);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{userId}/password",
     *     summary="Update user's password by ID",
     *     description="Update user password by ID",
     *     operationId="userPostPassword",
     *     tags={"User"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User info",
     *         @OA\JsonContent(
     *             required={"old_password","password","password_confirmation"},
     *             @OA\Property(property="old_password", type="string", format="password", example="password"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's password updated response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $userId
     * @param UserUpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updateUserPassword(int $userId, UserUpdatePasswordRequest $request){
        return $this->userService->updateUserPassword($userId, $request);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{userId}/friends",
     *     summary="Get user's friends by ID",
     *     description="Get user's friends by ID",
     *     operationId="userGetFriends",
     *     tags={"User"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed response",
     *         @OA\JsonContent(
     *             @OA\Property(property="friends", type="string", example="[]")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getFriends(int $userId){
        return $this->userService->getUserFriends($userId);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{userId}/friend-requests",
     *     summary="Get user's friend requests by ID",
     *     description="Get user's friend requests by ID",
     *     operationId="userGetFriendRequests",
     *     tags={"User"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed response",
     *         @OA\JsonContent(
     *             @OA\Property(property="friends", type="string", example="[]")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getFriendRequests(int $userId)
    {
        return $this->userService->getUserFriendRequests($userId);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{userId}/add/{friendId}",
     *     summary="Add friend by ID",
     *     description="Add friend by ID",
     *     operationId="userAddFriend",
     *     tags={"User"},
     *     security={ {"bearerToken": {} }},
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="ID of friend",
     *         in="path",
     *         name="friendId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's password updated response",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", example="User")
     *         )
     *     )
     * )
     * @OAS\SecurityScheme(
     *     securityScheme="bearerToken",
     *     type="http",
     *     scheme="bearer"
     * )
     *
     * @param int $userId
     * @param int $friendId
     * @return JsonResponse
     */
    public function addFriend(int $userId, int $friendId){
        return $this->userService->addFriend($userId, $friendId);
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


    public function updateUserAvatar(int $userId, UserUpdateAvatarRequest $request){
        $this->userService->updateUserAvatar($userId, $request);
    }
}
