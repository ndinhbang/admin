<?php

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

/** =============== Authentication ================= **/

Route::group(['prefix' => 'auth'], function () {
	Route::post('/login', 'AuthController@login')->name('auth.login');
	Route::post('/register', 'AuthController@register')->name('auth.register');
	Route::get('/activate/{token}', 'AuthController@activate')->name('auth.activate');
	Route::post('/password', 'AuthController@password')->name('auth.password');
	Route::post('/validate-password-reset', 'AuthController@validatePasswordReset')->name('auth.validate-password');
	Route::post('/reset', 'AuthController@reset')->name('auth.reset');
	// Route::post('/social/token', 'SocialAuthController@getToken')->name('auth.social.token');

	Route::post('/refresh-token', 'AuthController@refreshToken')->name('auth.refresh.token');

	Route::group(['middleware' => 'auth:api'], function () {
		Route::post('logout', 'AuthController@logout')->name('logout');

		Route::get('/user', 'UserController@current')->name('user.current');
	});
});

Route::group(['middleware' => 'auth:api'], function () {

	/** =============== Administrator =========== **/
	Route::group(['prefix' => 'admin'], function () {

		Route::get('/places', 'Admin\PlaceController@index')->name('admin.places');
		Route::post('/place', 'Admin\PlaceController@store')->name('admin.place.store');
		Route::get('/place/{place}', 'Admin\PlaceController@show')->name('admin.place.show');
		Route::put('/place/{place}', 'Admin\PlaceController@update')->name('admin.place.update');

		// Admin Users
		Route::get('/users', 'Admin\UserController@index')->name('admin.users');
		Route::post('/user', 'Admin\UserController@store')->name('admin.user.store');
		Route::put('/user/{user}', 'Admin\UserController@update')->name('admin.user.update');
	});

	/** =============== Profile ================= **/
	Route::group(['prefix' => 'profile'], function () {
		Route::post('/change-password', 'ProfileController@changePassword')->name('profile.change-password');
		Route::post('/update-profile', 'ProfileController@updateProfile')->name('profile.update-profile');
		Route::post('/update-avatar', 'ProfileController@updateAvatar')->name('profile.update-avatar');
	});

	/** =============== Place ================= **/
	Route::group(['prefix' => 'place'], function () {
		Route::get('/my', 'PlaceController@getMy')->name('place.my');
		Route::post('/update-logo', 'PlaceController@updateLogo')->name('profile.update-logo');
	});
	Route::resource('place', 'PlaceController');

	/** =============== Employee ================= **/
	Route::group(['prefix' => 'employee'], function () {
		Route::post('/update-avatar/{uuid}', 'EmployeeController@updateAvatar')->name('employee.update-avatar');
	});
	Route::resource('employee', 'EmployeeController');
	Route::resource('users', 'EmployeeController');

	/** =============== Voucher ================= **/
	Route::resource('voucher', 'VoucherController');

	/** =============== Category ================= **/
	Route::post('/category/position', 'CategoryController@updatePosition')->name('category.update-position');
	Route::resource('category', 'CategoryController');

	/** =============== Account: customer, supplier, shipper, employee ==== **/
	Route::resource('account', 'AccountController');

	/** =============== Role ================= **/
	Route::apiResource('roles', 'RoleController');

	/** =============== Supplies ================= **/
	Route::apiResource('supplies', 'SupplyController');

	/** =============== Product ================= **/
	Route::apiResource('product', 'ProductController');

	/** =============== Inventory ================= **/
	Route::apiResource('inventory', 'InventoryController');

	/** =============== Inventory Orders ================= **/
	Route::apiResource('inventory_order', 'InventoryOrderController');

	/** =============== Orders ================= **/
	Route::apiResource('orders', 'OrderController');

	/** =============== Areas & Tables ================= **/
	Route::apiResource('areas', 'AreaController');
	Route::apiResource('tables', 'TableController');

    Route::group(['prefix' => 'pos'], function () {
        /** =============== Pos Order ================= **/
        Route::get('orders', 'PosOrderController@index')->name('pos.orders.index');
        Route::post('orders', 'PosOrderController@store')->name('pos.orders.store');
        Route::get('orders/{order}', 'PosOrderController@show')->name('pos.orders.show');
        Route::put('orders/{order}', 'PosOrderController@update')->name('pos.orders.update');
        Route::delete('orders/{order}', 'PosOrderController@destroy')->name('pos.orders.destroy');

        Route::post('orders/{order}/add/{product}', 'PosOrderController@addItem')->name('pos.orders.add-item');
        Route::post('orders/{order}/update/{product}', 'PosOrderController@updateItem')->name('pos.orders.update-item');
        Route::post('orders/{order}/delete/{product}', 'PosOrderController@deleteItem')->name('pos.orders.delete-item');

        /** =============== Pos Product ================= **/
        Route::get('products', 'PosProductController@index')->name('pos.products.index');
    });
});
