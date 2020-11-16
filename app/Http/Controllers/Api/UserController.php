<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     *     path="/api/user/{userId}",
     *     summary="Get user by ID",
     *     description="Get user by ID",
     *     operationId="userGet",
     *     tags={"Auth"},
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
     *             @OA\Property(property="access_token", type="string", example="token")
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
