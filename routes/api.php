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

Route::post('login', 'AuthController@login')->name('login');
Route::post('password', 'AuthController@password')->name('password');
Route::post('refresh-token', 'AuthController@refreshToken')->name('token.refresh');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'AuthController@logout')->name('logout');

    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return ['user' => $user, 'abilities' => $user->getAbilities()];
    });
});
