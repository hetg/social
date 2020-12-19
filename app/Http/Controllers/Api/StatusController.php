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

    public function createPost(int $userId, PostCreateRequest $request){
        $this->statusService->createPost($userId, $request);
    }

    public function postReply(Request $request, $statusId){
        $this->validate($request, [
            "reply-{$statusId}" => 'required|max:1000',[
                'required' => 'The reply body is required.'
            ]
        ]);

        $status = Status::notReply()->find($statusId);

        if (!$status){
            return redirect()->back();
        }

        if (!Auth::user()->isFriendsWith($status->user) && Auth::user()->id !== $status->user->id){
            return redirect()->back();
        }

        $reply = Status::create([
            'body' => $request->input("reply-{$statusId}"),
        ])->user()->associate(Auth::user());

        $status->replies()->save($reply);

        return redirect()->back();
    }

    public function getLike($statusId){
        $status= Status::find($statusId);

        if (!$status){
            return redirect()->back();
        }

        if (Auth::user()->hasLikedStatus($status)){
            return redirect()->back();
        }

        $like = $status->likes()->create([]);
        Auth::user()->likes()->save($like);

        return redirect()->back();
    }

    public function getUnlike($statusId){
        $status= Status::find($statusId);

        if (!$status){
            return redirect()->back();
        }

        if (!Auth::user()->hasLikedStatus($status)){
            return redirect()->back();
        }

        Like::where([
            ['user_id', "=" ,Auth::user()->id],
            ['likeable_id', "=" , $status->id],
            ['likeable_type', "=" , get_class($status)],
        ])->delete();

        return redirect()->back();
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
