<?php

namespace App\Services;

use App\Events\ChatMessageWasReceived;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Models\Dialog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTAuth;

class MessageService
{

    /**
     * @var JWTAuth
     */
    private $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param int $chatId
     * @param int $userId
     * @param CreateMessageRequest $request
     * @return JsonResponse|void
     */
    public function createMessage(int $chatId, int $userId, CreateMessageRequest $request)
    {
        $chat = Dialog::find($chatId);
        $user = User::find($userId);

        if(!$chat) abort(404);
        if(!$user) abort(404);

        if ($user->id != $chat->first_user_id && $user->id != $chat->second_user_id) abort(403);

        if ($chat->first_user_id != $user->id) {
            $friend = User::find($chat->first_user_id);
        }
        else {
            $friend = User::find($chat->second_user_id);
        }

        if(!$friend) abort(404);

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'text' => $request->input('message'),
        ]);

        event(new ChatMessageWasReceived($chat, $message));

        return response()->json(['message' => 'Message created'],201);
    }

    /**
     * @param int $chatId
     * @return JsonResponse
     */
    public function getMessages(int $chatId)
    {
        $chat = Dialog::find($chatId);
        if(!$chat) abort(404);

        $user = Auth::user();

        if (!$user) abort(401);

        if ($user->id != $chat->first_user_id && $user->id != $chat->second_user_id) abort(404);

        if ($chat->first_user_id != $user->id) $friend = User::find($chat->first_user_id);
        else $friend = User::find($chat->second_user_id);

        if(!$friend) abort(404);

        $messages = $chat->messages()->get();

        return response()->json($messages);
    }

    /**
     * @param int $messageId
     * @return JsonResponse
     */
    public function deleteMessage(int $messageId)
    {
        $message = Message::find($messageId);

        if (!$message) abort(404);

        $user = Auth::user();

        if (!$user) abort(401);

        if ($message->sender_id != $user->id) abort(403);

        $message->delete();

        return response()->json('Deleted', 204);
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function getChats(int $userId)
    {
        $user = User::find($userId);

        if (!$user) abort(404);
        if ($user->id !== Auth::user()->id) abort(403);

        $dialogs = $user->dialogs();

        return response()->json($dialogs);
    }

    /**
     * @param int $userId
     * @param int $friendId
     * @return JsonResponse
     */
    public function createChat(int $userId, int $friendId)
    {
        $user = User::find($userId);
        $friend = User::find($friendId);

        if (!$user || !$friend) abort(404);
        if ($user->id != Auth::user()->id) abort(403);

        if(!$friend->isFriendsWith($user)) abort(403);

        $dialog = Dialog::where(['first_user_id' => $user->id, 'second_user_id' => $friend->id])->first();
        $dialog2 = Dialog::where(['first_user_id' => $friend->id, 'second_user_id' => $user->id])->first();

        if($dialog) return response()->json($dialog, 200);
        elseif ($dialog2) return response()->json($dialog2, 200);
        else{
            $dialog = Dialog::create([
                'first_user_id' => $user->id,
                'second_user_id' => $friend->id
            ]);

            return response()->json($dialog, 201);
        }
    }
}
