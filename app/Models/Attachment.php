<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{

    protected $table = 'attachments';

    protected $fillable = [
        'status_id',
        'type',
        'url'
    ];

    public function status(){
        return $this->belongsTo('App\Models\Status', 'status_id');
    }

    public function getUrl(){
        return asset('storage/attachments/images/'.$this->url);
    }

}
