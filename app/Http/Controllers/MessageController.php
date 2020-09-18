<?php

namespace App\Http\Controllers;

use App\Models\Dialog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function postMessage(Request $request, $chatId){
        $chat = Dialog::find($chatId);
        if(!$chat) return redirect()->route('home');

        if (Auth::user()->id != $chat->first_user_id && Auth::user()->id != $chat->second_user_id)
            return redirect()->route('home');

        if ($chat->first_user_id != Auth::user()->id) {
            $user = User::find($chat->first_user_id);
        }
        else {
            $user = User::find($chat->second_user_id);
        }

        if(!$user) return redirect()->route('home');

        $this->validate($request, [
            'message' => 'required|max:1000'
        ]);

        $chat->messages()->create([
            'sender_id' => Auth::user()->id,
            'text' => $request->input('message'),
        ]);

        return redirect()
            ->route('messages.show',['chatId' => $chatId]);
    }

    public function getMessages($chatId){
        $chat = Dialog::find($chatId);
        if(!$chat) return redirect()->route('home');

        if (Auth::user()->id != $chat->first_user_id && Auth::user()->id != $chat->second_user_id)
            return redirect()->route('home');

        if ($chat->first_user_id != Auth::user()->id) $user = User::find($chat->first_user_id);
        else $user = User::find($chat->second_user_id);

        if(!$user) return redirect()->route('home');

        $messages = $chat->messages()->get();

        return view('messages.dialog')
            ->with('messages',$messages)
            ->with('chat_id', $chatId)
            ->with('user',$user);
    }

    public function getDeleteMessage($messageId){
        $message = Message::find($messageId);

        if (!$message)
            return redirect()->back();

        $message->delete();

        return redirect()->back();
    }

    public function getDialogs(){
        $user = Auth::user();

        foreach ($user->dialogs() as $dialog){
            if (!count($dialog->messages()->get())){
                $dialog->delete();
            }
        }

        $dialogs = $user->dialogs()->sortByDesc('updated_at');

        return view('messages.dialogs')
            ->with([
                'dialogs' => $dialogs,
                'friends' => $user->friends()
            ]);
    }

    public function createDialog(Request $request){
        $userId = $request->input('userId');

        if (!$userId) {
            return redirect()->back();
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->back();
        }

        if(!$user->isFriendsWith(Auth::user())){
            return redirect()->back();
        }

        $dialog = Dialog::where(['first_user_id' => Auth::user()->id, 'second_user_id' => $userId])->first();
        $dialog2 = Dialog::where(['first_user_id' => $userId, 'second_user_id' => Auth::user()->id])->first();

        if($dialog) return redirect()->route('messages.show', ['chatId' => $dialog->id]);
        elseif ($dialog2) return redirect()->route('messages.show', ['chatId' => $dialog2->id]);
        else{
            $dialog = Dialog::create([
                'first_user_id' => Auth::user()->id,
                'second_user_id' => $userId
            ]);

            return redirect()->route('messages.show', ['chatId' => $dialog->id]);
        }
    }

}
