<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'statuses';

    protected $fillable = [
        'body',
    ];

    protected $with = array('attachments', 'likes', 'replies');

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function scopeNotReply($query){
        return $query->where('parent_id', null);
    }

    public function scopeReply($query){
        return $query->where('parent_id', '!=', null);
    }

    public function parent(){
        return $this->parent_id != 0 ? Status::find($this->parent_id) : null;
    }

    public function replies(){
        return $this->hasMany('App\Models\Status', 'parent_id');
    }

    public function likes(){
        return $this->morphMany('App\Models\Like', 'likeable');
    }

    public function attachments(){
        return $this->hasMany('App\Models\Attachment', 'status_id');
    }

}
