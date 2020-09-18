<?php

namespace App\Http\Controllers\Admin;

use App\Models\Dialog;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{

    public function index(){
        $users = User::all();

        return view('admin.index')
            ->with('users', $users);
    }

    public function getUserInfo($userId){
        $user = User::find($userId);

        $infos = array();

        $infos['messages'] = 0;

        foreach ($user->dialogs() as $dialog){
            $infos['messages'] += $dialog->countUserMessages($userId);
        }

        $infos['likes'] = count($user->likes()->get());

        $infos['statuses'] = count($user->statuses()->notReply()->get());
        $infos['comments'] = count($user->statuses()->reply()->get());

        return view('admin.info')
            ->with('user', $user)
            ->with('infos', $infos);
    }

    public function getUserStatuses($userId){
        $user = User::find($userId);

        $statuses = $user->statuses()->orderBy('created_at', 'desc')
            ->notReply()
            ->paginate(10);

        return view('admin.info.statuses')
            ->with('user', $user)
            ->with('statuses', $statuses);
    }

    public function getUserComments($userId){
        $user = User::find($userId);

        $comments = $user->statuses()->orderBy('created_at', 'desc')
            ->reply()->paginate(10);

        return view('admin.info.comments')
            ->with('user', $user)
            ->with('comments', $comments);
    }

    public function getUserMessages($userId){
        $user = User::find($userId);

        $messages = Dialog::getUserMessages($userId)->orderBy('created_at','desc')->paginate(10);

        return view('admin.info.messages')
            ->with('user', $user)
            ->with('messages', $messages);
    }

    public function getUserLikes($userId){
        $user = User::find($userId);

        $likes = $user->likes()->orderBy('created_at','desc')->paginate(10);
        //$messages = Dialog::getUserMessages($userId)->paginate(10);

        $likeable = [];

        foreach ($likes as $like){
            $likeable[] = $like->likeable()->get()[0];
        }

        return view('admin.info.likes')
            ->with('user', $user)
            ->with('likes', $likes)
            ->with('likeable', $likeable);
    }

}
