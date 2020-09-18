<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Webpatser\Uuid\Uuid;

class ProfileController extends Controller
{
    public function getProfile($user_id){
        $user = User::where('id', $user_id)->first();

        if (!$user){
            abort(404);
        }

        $statuses = $user->statuses()->orderBy('id', 'desc')->notReply()->paginate(10);

        return view('profile.index')
            ->with('user', $user)
            ->with('statuses', $statuses)
            ->with('authUserIsFriend', Auth::user()->isFriendsWith($user));
    }

    public function getEdit(){
        return view('profile.edit');
    }

    public function postEdit(Request $request){
        $this->validate($request, [
            'first_name' => 'alpha|max:50',
            'last_name' => 'alpha|max:50',
            'location' => 'max:20',
        ]);

        Auth::user()->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'location' => $request->input('location'),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Your profile has been updated.');
    }

    public function postEditPass(Request $request){
        $user = Auth::user();

        Validator::extend('old_password', function($attribute, $value) use ($user) {
            return Hash::check( $value, $user->password );
        },'Wrong password!');

        $this->validate($request, [
            'old_password' => 'required|min:6|max:32|old_password',
            'password' => 'required|min:6|max:32|confirmed',
            'password_confirmation' => 'required|min:6|max:32'
        ]);

        Auth::user()->update([
            'password' => bcrypt($request->input('password'))
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Your profile has been updated.');
    }

    public function postEditAvatar(Request $request){
        if($request->hasFile('avatar')) {
            $user = Auth::user();
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

                Auth::user()->update([
                    'avatar' => $filename
                ]);
            } else {
                return redirect()
                    ->back()
                    ->with('danger', 'Wrong file format.');
            }
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Your avatar has been updated.');
    }
}
