<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Status\PostCreateRequest;
use App\Http\Requests\User\UserUpdateAvatarRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\MessageService;
use App\Services\StatusService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var StatusService
     */
    private $statusService;

    /**
     * @var MessageService
     */
    private $messageService;

    public function __construct(UserService $userService, StatusService $statusService, MessageService $messageService)
    {
        $this->userService = $userService;
        $this->statusService = $statusService;
        $this->messageService = $messageService;
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

    /**
     * @OA\Post(
     *     path="/api/user/{userId}/accept/{friendId}",
     *     summary="Accept friend by ID",
     *     description="Accept friend by ID",
     *     operationId="userAcceptFriend",
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
    public function acceptFriend(int $userId, int $friendId){
        return $this->userService->acceptFriend($userId, $friendId);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/{userId}/delete/{friendId}",
     *     summary="Delete friend by ID",
     *     description="Delete friend by ID",
     *     operationId="userDeleteFriend",
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
    public function deleteFriend(int $userId, int $friendId){
        return $this->userService->deleteFriend($userId, $friendId);
    }


    /**
     * @OA\Delete(
     *     path="/api/user/{userId}/friend-requests/{friendId}",
     *     summary="Delete friend request by ID",
     *     description="Delete friend request by ID",
     *     operationId="userDeleteFriendRequest",
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
    public function deleteFriendRequest(int $userId, int $friendId){
        return $this->userService->deleteFriendRequest($userId, $friendId);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{userId}/feed",
     *     summary="Get feed by user ID",
     *     description="Get feed by user ID",
     *     operationId="feedGet",
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
     *             @OA\Property(property="post", type="object", example="Post")
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
    public function getUserFeed(int $userId){
        return $this->userService->getUserFeed($userId);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{userId}/posts",
     *     summary="Get posts by user ID",
     *     description="Get posts by user ID",
     *     operationId="postsGet",
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
     *             @OA\Property(property="post", type="object", example="Post")
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
    public function getUserPosts(int $userId){
        return $this->userService->getUserPosts($userId);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{userId}/posts",
     *     summary="Update user's password by ID",
     *     description="Update user password by ID",
     *     operationId="userCreatePost",
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
     *             required={"status"},
     *             @OA\Property(property="status", type="string", format="text", example="reply"),
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
     * @param PostCreateRequest $request
     * @return void
     */
    public function createPost(int $userId, PostCreateRequest $request){
        $this->statusService->createPost($userId, $request);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{userId}/posts/{postId}/reply",
     *     summary="Update user's password by ID",
     *     description="Update user password by ID",
     *     operationId="userCreatePostReply",
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
     *         description="ID of post",
     *         in="path",
     *         name="postId",
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
     *             required={"status"},
     *             @OA\Property(property="status", type="string", format="text", example="reply"),
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
     * @param int $postId
     * @param PostCreateRequest $request
     * @return void
     */
    public function createPostReply(int $userId, int $postId, PostCreateRequest $request){
        $this->statusService->createPostReply($userId, $postId, $request);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{userId}/chats",
     *     summary="Get chats by user ID",
     *     description="Get chats by user ID",
     *     operationId="chatsGet",
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
     *             @OA\Property(property="post", type="object", example="Post")
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
    public function getChats(int $userId){
        return $this->messageService->getChats($userId);
    }

    public function updateUserAvatar(int $userId, UserUpdateAvatarRequest $request){
        $this->userService->updateUserAvatar($userId, $request);
    }
}
