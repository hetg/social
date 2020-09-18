<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Home
 */
Route::get('/', [
    'uses' => 'HomeController@index',
    'as' => 'home'
]);

/**
 * Authentication
 */
Route::get('/signup', [
    'uses' => 'AuthController@getSignUp',
    'as' => 'auth.signup',
    'middleware' => ['guest']
]);

Route::post('/signup', [
    'uses' => 'AuthController@postSignUp',
    'middleware' => ['guest']
]);

Route::get('/signup/{token}', [
    'uses' => 'AuthController@getEmailConf',
    'as' => 'auth.confirm',
    'middleware' => ['guest']
]);

Route::get('/signin', [
    'uses' => 'AuthController@getSignIn',
    'as' => 'auth.signin',
    'middleware' => ['guest']
]);

Route::post('/signin', [
    'uses' => 'AuthController@postSignIn',
    'as' => 'auth.signin',
    'middleware' => ['guest']
]);

Route::get('/signout', [
    'uses' => 'AuthController@getSignOut',
    'as' => 'auth.signout',
    'middleware' => ['auth']
]);

/**
 * Search
 */
Route::get('/search', [
    'uses' => 'SearchController@getResults',
    'as' => 'search.results',
    'middleware' => ['auth']
]);


Route::middleware(['auth'])->group(function (){
    /**
     * User Profile
     */
    Route::get('/id{user_id}', [
        'uses' => 'ProfileController@getProfile',
        'as' => 'profile.index'
    ]);

    Route::get('/edit', [
        'uses' => 'ProfileController@getEdit',
        'as' => 'profile.edit'
    ]);

    Route::post('/edit/infoupdate', [
        'uses' => 'ProfileController@postEdit',
        'as' => 'profile.editPost'
    ]);

    Route::post('/edit/passupdate', [
        'uses' => 'ProfileController@postEditPass',
        'as' => 'profile.editPass'
    ]);

    Route::post('/edit/avatarupdate', [
        'uses' => 'ProfileController@postEditAvatar',
        'as' => 'profile.editAvatar'
    ]);

    /**
     * Friends
     */
    Route::get('/friends', [
        'uses' => 'FriendController@getIndex',
        'as' => 'friends.index',
        'middleware' => ['auth']
    ]);

    Route::get('/friends/add/id{user_id}', [
        'uses' => 'FriendController@getAdd',
        'as' => 'friends.add',
        'middleware' => ['auth']
    ]);

    Route::get('/friends/accept/id{user_id}', [
        'uses' => 'FriendController@getAccept',
        'as' => 'friends.accept',
        'middleware' => ['auth']
    ]);

    Route::post('/friends/delete/id{user_id}', [
        'uses' => 'FriendController@postDelete',
        'as' => 'friends.delete',
        'middleware' => ['auth']
    ]);

    /**
     * Statuses
     */
    Route::post('/status', [
        'uses' => 'StatusController@postStatus',
        'as' => 'status.post',
        'middleware' => ['auth']
    ]);

    Route::post('/status/{statusId}/reply', [
        'uses' => 'StatusController@postReply',
        'as' => 'status.reply',
        'middleware' => ['auth']
    ]);

    Route::get('/status/{statusId}/like', [
        'uses' => 'StatusController@getLike',
        'as' => 'status.like',
        'middleware' => ['auth']
    ]);

    Route::get('/status/{statusId}/unlike', [
        'uses' => 'StatusController@getUnlike',
        'as' => 'status.unlike',
        'middleware' => ['auth']
    ]);

    Route::get('/status/{statusId}/delete', [
        'uses' => 'StatusController@deletePost',
        'as' => 'status.delete',
        'middleware' => ['auth']
    ]);

    /**
     * Messages
     */
    Route::get('/im/chat{chatId}', [
        'uses' => 'MessageController@getMessages',
        'as' => 'messages.show',
        'middleware' => ['auth']
    ]);

    Route::post('/im/send/{chatId}', [
        'uses' => 'MessageController@postMessage',
        'as' => 'messages.send',
        'middleware' => ['auth']
    ]);

    Route::get('/im/delete/{messageId}', [
        'uses' => 'MessageController@getDeleteMessage',
        'as' => 'message.delete',
        'middleware' => ['auth', 'admin']
    ]);

    Route::get('/im', [
        'uses' => 'MessageController@getDialogs',
        'as' => 'dialogs.show',
        'middleware' => ['auth']
    ]);

    Route::get('/im/create', [
        'uses' => 'MessageController@createDialog',
        'as' => 'dialogs.create',
        'middleware' => ['auth']
    ]);
});

/**
 * Notifications
 */
Route::get('/notifications', [
    'uses' => 'NotificationController@getNotifications',
]);

/**
 * Admin section
 */

Route::prefix('admin')->middleware(['auth','admin'])->group(function () {
    Route::get('/', [
        'uses' => 'Admin\MainController@index',
        'as' => 'admin.index',
    ]);

    Route::get('/user/{userId}', [
        'uses' => 'Admin\MainController@getUserInfo',
        'as' => 'admin.info',
    ]);

    Route::get('/user/{userId}/messages', [
        'uses' => 'Admin\MainController@getUserMessages',
        'as' => 'admin.info.messages',
    ]);

    Route::get('/user/{userId}/statuses', [
        'uses' => 'Admin\MainController@getUserStatuses',
        'as' => 'admin.info.statuses',
    ]);

    Route::get('/user/{userId}/comments', [
        'uses' => 'Admin\MainController@getUserComments',
        'as' => 'admin.info.comments',
    ]);

    Route::get('/user/{userId}/likes', [
        'uses' => 'Admin\MainController@getUserLikes',
        'as' => 'admin.info.likes',
    ]);
});

Route::get('/{locale}', function ($locale) {
    App::setLocale($locale);

    return redirect()->home();
});
