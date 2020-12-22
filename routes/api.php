<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::get('confirm/{token}', 'AuthController@emailConf');
    Route::group([
        'middleware' => 'auth'
    ], function ($router) {
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
    }
    );
});

Route::middleware('api')->get('/users/{query}', 'UserController@find');

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'user'
], function ($router) {
    Route::get('{userId}', 'UserController@getUser');
    Route::post('{userId}', 'UserController@updateUser');
    Route::post('{userId}/password', 'UserController@updateUserPassword');
    Route::post('{userId}/avatar', 'UserController@updateUserAvatar');
    Route::get('{userId}/friends', 'UserController@getFriends');
    Route::get('{userId}/friend-requests', 'UserController@getFriendRequests');
    Route::delete('{userId}/friend-requests/{friendId}', 'UserController@deleteFriendRequest');
    Route::post('{userId}/add/{friendId}', 'UserController@addFriend');
    Route::post('{userId}/accept/{friendId}', 'UserController@acceptFriend');
    Route::delete('{userId}/delete/{friendId}', 'UserController@deleteFriend');
    Route::get('{userId}/feed', 'UserController@getUserFeed');
    Route::get('{userId}/posts', 'UserController@getUserPosts');
    Route::post('{userId}/posts', 'UserController@createPost');
    Route::post('{userId}/posts/{postId}/reply', 'UserController@createPostReply');
    Route::get('{userId}/chats', 'UserController@getChats');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'post'
], function ($router) {
    Route::get('{postId}', 'StatusController@getPost');
    Route::delete('{postId}', 'StatusController@deletePost');
    Route::post('{postId}/like/{userId}', 'StatusController@postLike');
    Route::delete('{postId}/like/{userId}', 'StatusController@deleteLike');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'chat'
], function ($router) {
    Route::post('create/{userId}/with/{friendId}', 'MessageController@createChat');
    Route::get('{chatId}', 'MessageController@getMessages');
    Route::post('{chatId}/send/{userId}', 'MessageController@postMessage');
    Route::delete('{chatId}/message/{messageId}', 'MessageController@deleteMessage');
});
