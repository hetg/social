<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmUser extends Model
{
    protected $table = "conf_users";

    protected $fillable = [
        'email',
        'token'
    ];
}
