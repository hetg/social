<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dialog extends Model
{
    protected $table = 'dialogs';

    protected $fillable = [
        'first_user_id',
        'second_user_id',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'first_user_id');
    }

    public function users(){
        return ['first' => User::find($this->first_user_id), 'second' => User::find($this->second_user_id)];
    }

    public function lastMessage(){
        return $this->hasMany('App\Models\Message', 'chat_id')->get()->last();
    }

    public function messages(){
        return $this->hasMany('App\Models\Message', 'chat_id');
    }

    public function count(){
        return count($this->messages()->get());
    }

    public function userMessages($userId){
        return $this->messages()->where('sender_id', $userId);
    }

    public function countUserMessages($userId){
        return count($this->userMessages($userId)->get());
    }

    public static function getUserMessages($userId){
        return Message::where('sender_id', $userId);
    }
}
