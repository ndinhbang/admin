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

	/** =============== Manage =========== **/
	Route::get('manage/overview', 'ManageController@overview');
	Route::get('manage/dailyRevenues', 'ManageController@dailyRevenues');

	/** =============== Profile ================= **/
	Route::group(['prefix' => 'profile'], function () {
		Route::post('/change-password', 'ProfileController@changePassword')->name('profile.change-password');
		Route::post('/update-profile', 'ProfileController@updateProfile')->name('profile.update-profile');
		Route::post('/update-avatar', 'ProfileController@updateAvatar')->name('profile.update-avatar');
	});

	/** =============== Place ================= **/
	Route::group(['prefix' => 'place'], function () {
		Route::get('/current', 'PlaceController@current')->name('place.current');
		Route::post('/update-logo', 'PlaceController@updateLogo')->name('place.update-logo');
	});
	Route::put('/place/{place}/printers', 'PlaceController@printers')->name('place.printers');
	Route::resource('place', 'PlaceController');

	/** =============== Employee ================= **/
	Route::group(['prefix' => 'employee'], function () {
		Route::post('/update-avatar/{uuid}', 'EmployeeController@updateAvatar')->name('employee.update-avatar');
	});

	Route::get('/employee/all_active', 'EmployeeController@all_active')->name('employee.all-active');
	Route::resource('employee', 'EmployeeController');
	Route::resource('users', 'EmployeeController');

	/** =============== Voucher ================= **/
	Route::get('voucher/overview', 'VoucherController@overview');
	Route::resource('voucher', 'VoucherController');

	/** =============== Category ================= **/
	Route::post('/category/position', 'CategoryController@updatePosition')->name('category.update-position');
	Route::get('/category/all_active', 'CategoryController@all_active')->name('category.all-active');
	Route::resource('category', 'CategoryController');

	/** =============== Account: customer, supplier, shipper, employee ==== **/
	Route::resource('account', 'AccountController');

    Route::resource('segment', 'SegmentController');

	/** =============== Role ================= **/
	Route::apiResource('roles', 'RoleController');

	/** =============== Supply ================= **/
	Route::apiResource('supply', 'SupplyController');

	/** =============== Product ================= **/
	Route::apiResource('product', 'ProductController');

	/** =============== Inventory ================= **/
	Route::get('inventory/almostoos', 'InventoryController@almostOos'); // Almost out of stock.
	Route::get('inventory/statistic', 'InventoryController@statistic');
	Route::apiResource('inventory', 'InventoryController');

	/** =============== Inventory Orders ================= **/
	Route::post('inventory_order/{uuid}/pay-debt', 'InventoryOrderController@payDebt')->name('inventory_order.pay_debt');
	Route::apiResource('inventory_order', 'InventoryOrderController');

	/** =============== Inventory Takes ================= **/
	Route::apiResource('inventory_take', 'InventoryTakeController');

	/** =============== Orders ================= **/
	Route::apiResource('orders', 'OrderController');

    /** =============== Promotion ================= **/

    Route::apiResource('promotion', 'PromotionController');
    Route::put('promotion/{promotion}/status', 'PromotionController@setStatus')->name('promotion.status');

	/** =============== Report ================= **/
	Route::get('report/revenues', 'ReportController@revenues');
	Route::get('report/profits', 'ReportController@profits');
	Route::get('report/net-profits', 'ReportController@netProfits');
	Route::get('report/export', 'ReportController@export');

	/** =============== Areas & Tables ================= **/
    Route::put('areas/{area}/add-table', 'AreaController@addTable')->name('areas.add-table');
    Route::delete('areas/{area}/delete-table/{table}', 'AreaController@deleteTable')->name('areas.delete-table');
	Route::apiResource('areas', 'AreaController');
    Route::apiResource('tables', 'TableController');

    /** =============== Config ================= **/
//    Route::put('config/info-print', 'ConfigController@configPrintInfo')->name('config.infoprint');
    Route::put('config/print', 'ConfigController@configPrint')->name('config.print');
    Route::put('config/screen2nd', 'ConfigController@configScreen2nd')->name('config.screen2nd');
    Route::put('config/sale', 'ConfigController@configSale')->name('config.sale');

	Route::group(['prefix' => 'pos'], function () {
		/** =============== Pos Report ================= **/
		Route::get('report', 'PosReportController@index')->name('pos.report.index');
		
		/** =============== Pos Order ================= **/
		Route::get('orders', 'PosOrderController@index')->name('pos.orders.index');
		Route::post('orders', 'PosOrderController@store')->name('pos.orders.store');
        Route::get('orders/inactive', 'PosOrderController@inactive')->name('pos.orders.inactive');
		Route::get('orders/{order}', 'PosOrderController@show')->name('pos.orders.show');
		Route::put('orders/{order}', 'PosOrderController@update')->name('pos.orders.update');

		Route::put('orders/{order}/printed', 'PosOrderController@printed')->name('pos.orders.printed');
		Route::put('orders/{order}/canceled', 'PosOrderController@canceled')->name('pos.orders.canceled');

		/** =============== Pos Product ================= **/
		Route::get('products', 'PosProductController@index')->name('pos.products.index');
		Route::get('categories', 'PosCategoryController@index')->name('pos.categories.index');

        Route::get('promotion/current', 'PromotionController@current')->name('pos.promotion.current');
	});
});
