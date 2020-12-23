<?php

namespace App\Services;

use App\Events\ChatMessageDeleted;
use App\Events\ChatMessageReceived;
use App\Events\ChatMessageUpdated;
use App\Events\NewMessageReceived;
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
        $chat->updated_at = $message->created_at;
        $chat->save();

        event(new ChatMessageReceived($chat, $message, $user));
        event(new NewMessageReceived($chat, $message, $user, $friend));

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
        foreach ($messages as $message){
            if ($message->sender_id !== $user->id && !$message->read) {
                $message->read = true;
                $message->save();

                $sender = User::find($message->sender_id);
                if ($sender) {
                    event(new ChatMessageUpdated($chat, $message, $sender));
                }
            }
        }

        return response()->json($messages);
    }

    /**
     * @param int $messageId
     * @return JsonResponse
     */
    public function deleteMessage(int $messageId)
    {
        $message = Message::find($messageId);
        $chat = $message->chat;

        if (!$message) abort(404);

        $user = Auth::user();

        if (!$user) abort(401);

        if ($message->sender_id != $user->id) abort(403);

        $message->delete();
        $chat->updated_at = $chat->getLastMessageAttribute()->created_at;
        $chat->save();

        event(new ChatMessageDeleted($chat, $messageId));

        return response()->json('Deleted', 204);
    }

    /**
     * @param int $chatId
     * @param int $messageId
     * @param int $userId
     * @return JsonResponse
     */
    public function readMessage(int $chatId, int $messageId, int $userId)
    {
        $chat = Dialog::find($chatId);
        $user = User::find($userId);
        $message = Message::find($messageId);

        if(!$chat) abort(404);
        if(!$user) abort(404);
        if(!$message) abort(404);

        if ($user->id != $chat->first_user_id && $user->id != $chat->second_user_id) abort(403);

        if ($chat->first_user_id != $user->id) {
            $friend = User::find($chat->first_user_id);
        }
        else {
            $friend = User::find($chat->second_user_id);
        }

        if(!$friend) abort(404);

        $message->read = $message->sender_id == $user->id ? $message->read : true;
        $message->save();

        $sender = User::find($message->sender_id);
        if ($sender && $message->read) {
            event(new ChatMessageUpdated($chat, $message, $sender));
        }

        return response()->json($message);
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
