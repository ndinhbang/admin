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

// Route::get('/print/item/{order}/{itemId}/{stt}', 'PrintController@printItem')->name('print.order.item');
// Route::get('/print/order/{order}', 'PrintController@printOrder')->name('print.order');
Route::get('/print/preview/{order}', 'PrintController@preview')->name('print.preview');
Route::get('/print/report/{place}', 'PrintController@report')->name('print.shift');


// Route::get('/home', 'HomeController@index')->name('home');
