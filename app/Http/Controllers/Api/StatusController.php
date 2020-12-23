<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Status\PostCreateRequest;
use App\Models\Like;
use App\Models\Status;
use App\Services\StatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Webpatser\Uuid\Uuid;

class StatusController extends Controller
{

    /**
     * @var StatusService
     */
    private $statusService;

    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * @OA\Get(
     *     path="/api/post/{postId}",
     *     summary="Get post by ID",
     *     description="Get post by ID",
     *     operationId="postGet",
     *     tags={"Post"},
     *     security={ {"bearerToken": {} }},
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
     * @param int $postId
     * @return JsonResponse
     */
    public function getPost(int $postId){
        return $this->statusService->getPost($postId);
    }

    /**
     * @OA\Post(
     *     path="/api/post/{postId}/like/{userId}",
     *     summary="Update user's password by ID",
     *     description="Update user password by ID",
     *     operationId="postCreateLike",
     *     tags={"Post"},
     *     security={ {"bearerToken": {} }},
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
     * @param int $postId
     * @param int $userId
     * @return JsonResponse
     */
    public function postLike(int $postId, int $userId){
        return $this->statusService->createLike($postId, $userId);
    }

    /**
     * @OA\Delete(
     *     path="/api/post/{postId}/like/{userId}",
     *     summary="Delete like by ID",
     *     description="Delete like by ID",
     *     operationId="postDeleteLike",
     *     tags={"Post"},
     *     security={ {"bearerToken": {} }},
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
     * @param int $postId
     * @param int $userId
     * @return JsonResponse
     */
    public function deleteLike(int $postId, int $userId){
        return $this->statusService->deleteLike($postId, $userId);
    }

    /**
     * @OA\Delete(
     *     path="/api/post/{postId}",
     *     summary="Delete post by ID",
     *     description="Delete post by ID",
     *     operationId="postDelete",
     *     tags={"Post"},
     *     security={ {"bearerToken": {} }},
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
     * @param $postId
     * @return JsonResponse
     */
    public function deletePost($postId){
        $this->statusService->deletePost($postId);
        return response()->json(['message' => 'Post deleted'], 204);
    }

}
