<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Webpatser\Uuid\Uuid;

class StatusController extends Controller
{
    public function postStatus(Request $request){
        $this->validate($request, [
            'status' => 'required|max:1000'
        ]);

        $status = Auth::user()->statuses()->create([
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

                    return redirect()
                        ->back()
                        ->with('danger', 'Wrong file format.');
                }
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Status posted.');
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

    public function deletePost($statusId){
        $status= Status::find($statusId);

        if (!$status){
            return redirect()->back();
        }

        if (Auth::user()->id != $status->user->id && !Auth::user()->isAdmin()){
            return redirect()->back();
        }

        $attachments = $status->attachments()->get();

        foreach ($attachments as $attachment){
            $file = public_path('storage/attachments/images/' . $attachment->url);

            if (File::exists($file)) {
                unlink($file);
            }

            $attachment->delete();
        }

        $replies = Status::where([
            ['parent_id', "=" , $status->id],
        ])->get();
        foreach ($replies as $reply) {
            Like::where([
                ['likeable_id', "=" , $reply->id],
                ['likeable_type', "=" , get_class($reply)],
            ])->delete();
            $reply->delete();
        }
        Like::where([
            ['likeable_id', "=" , $status->id],
            ['likeable_type', "=" , get_class($status)],
        ])->delete();
        $status->delete();

        return redirect()->back();
    }

}
