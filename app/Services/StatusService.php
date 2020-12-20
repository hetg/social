<?php

namespace App\Services;

use App\Http\Requests\Status\PostCreateRequest;
use App\Http\Requests\User\UserUpdateAvatarRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Like;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Webpatser\Uuid\Uuid;

class StatusService
{

    /**
     * @param int $postId
     * @return JsonResponse
     */
    public function getPost(int $postId){
        $post = Status::find($postId);

        if (!$post){
            abort(404);
        }

        return response()->json($post);
    }

    /**
     * @param int $userId
     * @param PostCreateRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function createPost(int $userId, PostCreateRequest $request)
    {
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        $status = $user->statuses()->create([
            'body' => $request->input('status'),
        ]);

        if($request->hasFile('attachments')) {
            foreach ($request->attachments as $attachment){
                $filename = Uuid::generate() . '.' . $attachment->getClientOriginalExtension();

                if ($attachment->getClientOriginalExtension() == "jpg" || $attachment->getClientOriginalExtension() == "png" || $attachment->getClientOriginalExtension() == "jpeg") {

                    $img = Image::make($attachment->getRealPath());
                    $img->stream(); // <-- Key point

                    Storage::disk('public')->put('attachments/images/'.$filename, $img, 'public');

                    $status->attachments()->create([
                        'url' => $filename
                    ]);
                } else {
                    $this->deletePost($status->id);

                    return response()->json(['message' => 'Wrong file format.'], 400);
                }
            }
        }

        return response()->json(['message' => 'Status posted'], 201);
    }

    /**
     * @param int $userId
     * @param int $postId
     * @param PostCreateRequest $request
     * @return JsonResponse
     */
    public function createPostReply(int $userId, int $postId, PostCreateRequest $request): JsonResponse
    {
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        $status = Status::notReply()->find($postId);

        if (!$status){
            abort(404);
        }

        if (!$user->isFriendsWith($status->user) && $user->id !== $status->user->id){
            abort(403);
        }

        $reply = Status::create([
            'body' => $request->input("status"),
        ])->user()->associate($user);

        $status->replies()->save($reply);

        return response()->json(['message' => 'Reply posted'], 201);
    }

    /**
     * @param int $postId
     */
    public function deletePost(int $postId)
    {
        $post = Status::find($postId);

        if (!$post){
            abort(404);
        }

        if (Auth::user()->id != $post->user->id && !Auth::user()->isAdmin()){
            abort(403);
        }

        $attachments = $post->attachments()->get();

        foreach ($attachments as $attachment){
            $file = public_path('storage/attachments/images/' . $attachment->url);

            if (File::exists($file)) {
                unlink($file);
            }

            $attachment->delete();
        }

        $replies = Status::where([
            ['parent_id', "=" , $post->id],
        ])->get();
        foreach ($replies as $reply) {
            Like::where([
                ['likeable_id', "=" , $reply->id],
                ['likeable_type', "=" , get_class($reply)],
            ])->delete();
            $reply->delete();
        }
        Like::where([
            ['likeable_id', "=" , $post->id],
            ['likeable_type', "=" , get_class($post)],
        ])->delete();
        $post->delete();
    }

    /**
     * @param int $postId
     * @param int $userId
     * @return JsonResponse
     */
    public function createLike(int $postId, int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        $post = Status::find($postId);

        if (!$post){
            abort(404);
        }

        if ($user->hasLikedStatus($post)){
            abort(200);
        }

        $like = $post->likes()->create([]);
        $user->likes()->save($like);

        return response()->json(['message' => 'Like added'], 201);
    }

    /**
     * @param int $postId
     * @param int $userId
     * @return JsonResponse
     */
    public function deleteLike(int $postId, int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user){
            abort(404);
        }

        $post = Status::find($postId);

        if (!$post){
            abort(404);
        }

        if (!$user->hasLikedStatus($post)){
            abort(200);
        }

        Like::where([
            ['user_id', "=" ,$user->id],
            ['likeable_id', "=" , $post->id],
            ['likeable_type', "=" , get_class($post)],
        ])->delete();

        return response()->json(['message' => 'Like removed'], 204);
    }
}
