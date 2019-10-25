<?php

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Route;

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



Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'AuthController@login')->name('auth.login');
    Route::post('/register', 'AuthController@register')->name('auth.register');
    Route::get('/activate/{token}', 'AuthController@activate')->name('auth.activate');
    Route::post('/password', 'AuthController@password')->name('auth.password');
    Route::post('/validate-password-reset', 'AuthController@validatePasswordReset')->name('auth.validate-password');
    Route::post('/reset', 'AuthController@reset')->name('auth.reset');
    Route::post('/social/token', 'SocialAuthController@getToken')->name('auth.social.token');

    Route::post('/refresh-token', 'AuthController@refreshToken')->name('auth.refresh.token');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', 'AuthController@logout')->name('logout');

        Route::get('/user', function (Request $request) {
            $user = $request->user();
            return ['user' => $user, 'abilities' => $user->getAbilities()];
        });
    });
});

Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'profile'], function () {
        Route::post('/change-password', 'ProfileController@changePassword')->name('profile.change-password');
        Route::post('/update-profile', 'ProfileController@updateProfile')->name('profile.update-profile');
        Route::post('/update-avatar', 'ProfileController@updateAvatar')->name('profile.update-avatar');
    });

    Route::group(['prefix' => 'place'], function () {
        Route::get('/my', 'PlaceController@getMy')->name('place.my');
    });
    
    Route::resource('place', 'PlaceController');
});