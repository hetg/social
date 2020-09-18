<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'sender_id',
        'text',
    ];

    public function from(){
        return $this->belongsTo('App\Models\User', 'sender_id');
    }

    public function chat(){
        return $this->belongsTo('App\Models\Dialog', 'chat_id');
    }

}
