<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'location',
        'validate',
        'avatar',
        'admin',
    ];

    protected $hidden = [
        'remember_token',
        'password'
    ];

    protected $appends = ['is_online'];

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function getIsOnlineAttribute()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function getInfo(){
        return $this->belongsTo('App\Models\Info', 'user_id');
    }

    public function getName(){
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFirstName(){
        return $this->first_name;
    }

    public function getAvatarUrl($size = 'original'){
        if ($this->avatar == "default.png"){
            return asset($this->avatar);
        }

        switch ($size){
            case 'small':
                return asset('storage/images/avatar/smalls/'.$this->avatar);

            case 'original':
            default:
                return asset('storage/images/avatar/'.$this->avatar);
        }

    }

    public function statuses(){
        return $this->hasMany('App\Models\Status', 'user_id');
    }

    public function dialogs(){
        $chats = $this->hasMany('App\Models\Dialog', 'first_user_id')->get()
            ->merge($this->hasMany('App\Models\Dialog', 'second_user_id')->get())->sortByDesc('updated_at');

        $chatsArray = [];
        foreach ($chats as $chat){
            $chatsArray[] = $chat;
        }

        return $chatsArray;
    }

    public function likes(){
        return $this->hasMany('App\Models\Like', 'user_id');
    }

    public function friendsOfMine(){
        return $this->belongsToMany('App\Models\User', 'friends', 'user_id', 'friend_id');
    }

    public function friendOf(){
        return $this->belongsToMany('App\Models\User', 'friends', 'friend_id', 'user_id');
    }

    public function friends(){
        return $this
            ->friendsOfMine()
            ->wherePivot('accepted', true)
            ->get()
            ->merge($this
                ->friendOf()
                ->wherePivot('accepted', true)
                ->get());
    }

    public function friendRequests(){
        return $this
            ->friendsOfMine()
            ->wherePivot('accepted', false)
            ->get();
    }

    public function friendRequestsPending(){
        return $this
            ->friendOf()
            ->wherePivot('accepted', false)
            ->get();
    }

    public function hasFriendRequestPending(User $user){
        return (bool) $this->friendRequestsPending()->where('id', $user->id)->count();
    }

    public function hasFriendRequestReceived(User $user){
        return (bool) $this->friendRequests()->where('id', $user->id)->count();
    }

    public function addFriend(User $user){
        $this->friendOf()->attach($user->id);
    }

    public function deleteFriend(User $user){
        $this->friendOf()->detach($user->id);
        $this->friendsOfMine()->detach($user->id);
    }

    public function acceptFriendRequest(User $user){
        $this
            ->friendRequests()
            ->where('id', $user->id)
            ->first()
            ->pivot
            ->update([
                'accepted' => true
            ]);
    }

    public function isFriendsWith(User $user){
        return (bool) $this->friends()->where('id', $user->id)->count();
    }

    public function hasLikedStatus(Status $status){
        return (bool) $status->likes->where('user_id', $this->id)->count();
    }

    public function isAdmin(){
        return (bool) $this->admin;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}

