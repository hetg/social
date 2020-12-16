<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
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
