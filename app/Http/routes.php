<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
* Default route
*/
Route::get('/', 'WelcomeController@index');

/**
* Products route
*/
Route::group(['prefix' => 'products'], function(){
	//get
	Route::get('/', ['uses' => 'ProductController@allProduct']);

	Route::get('/{id_ean}', ['uses' => 'ProductController@oneProduct'])
	->where('id_ean', '[0-9]+');

	Route::get('/brands', ['uses' => 'ProductController@allBrand']);

	Route::get('/device/{id_device}', ['uses' => 'ProductController@productForDevice'])
	->where('id_device', '[0-9]+');

	Route::get('/amazone_request/{id}', ['uses' => 'ProductController@amazone']); //new, from : /amazone_request/products/{id}

	//post, put
	Route::post('/', ['uses' => 'ProductController@store']);

	Route::put('/', ['uses' => 'ProductController@update']);

	Route::post('/sendinfo', ['uses' => 'ProductController@sendInfos']);

	Route::put('/remove_quantity/{id_product}', ['uses' => 'ProductController@removeQuantity']) //new, from : /products/{id}/remove/{qty}
	->where('id_product', '[0-9]+');


	//debug, test
	Route::get('/new_product', ['uses' => 'ProductController@create']); 

	Route::get('/edit_product', ['uses' => 'ProductController@edit']);
	
});

/**
* Products manager route
*/
Route::group(['prefix' => 'pmanage_products'], function(){

	//get
	Route::get('/device/{id_device}', ['uses' => 'ProductController@pManager'])
	->where('id_device', '[0-9]+');

	Route::get('/{id}', ['uses' => 'ProductController@pManager'])
	->where('id', '[0-9]+');

	Route::get('/box/{id_box}', ['uses' => 'ProductController@pManager'])
	->where('id_box', '[0-9]+');

	Route::get('/bigdata_brand/{id}', ['uses' => 'ProductController@pManager'])
	->where('id', '[0-9]+');

	Route::get('/', ['uses' => 'ProductController@pManager']);
});

/**
* Devices route
*/
Route::group(['prefix' => 'devices'], function(){

	//get
	Route::get('/', ['uses' => 'DeviceController@allDevice']);

	Route::get('/{id}', ['uses' => 'DeviceController@oneDevice'])
	->where('id', '[0-9]+');

	Route::get('/brand/{id}/{ignore?}', ['uses' => 'DeviceController@allDeviceByBrand'])
	->where(['id' => '[0-9]+', 'ignore' => '[a-zA-Z]+']);

	Route::get('/phone/brand/{id}/{ignore?}', ['uses' => 'DeviceController@allDeviceByType'])
	->where(['id' => '[0-9]+', 'ignore' => '[a-zA-Z]+']);

	Route::get('/tablet/brand/{id}/{ignore?}', ['uses' => 'DeviceController@allDeviceByType'])
	->where(['id' => '[0-9]+', 'ignore' => '[a-zA-Z]+']);

	//post, put
	Route::post('/', ['uses' => 'DeviceController@create']);

	Route::put('/', ['uses' => 'DeviceController@edit']);

});

/**
* Collections route
*/
Route::group(['prefix' => 'collections'], function(){

	//get
	Route::get('/', ['uses' => 'CollectionController@allCollection']);

	Route::get('/{id}', ['uses' => 'CollectionController@oneCollection'])
	->where('id', '[0-9]+');

	//post, put
	Route::post('/', ['uses' => 'CollectionController@store']);

	Route::put('/', ['uses' => 'CollectionController@update']);
});

/**
* Brands route
*/
Route::group(['prefix' => 'brands'], function(){

	//get
	Route::get('/', ['uses' => 'BrandController@allBrand']);

	Route::get('/withdevice', ['uses' => 'BrandController@getWithDevice']);

	Route::get('/popular', ['uses' => 'BrandController@getPopular']);

});

/**
* Customers route
*/
Route::group(['prefix' => 'customers'], function(){

	//get
	Route::get('/', ['uses' => 'CustomerController@allCustomer']);

	Route::get('/{id}', ['uses' => 'CustomerController@oneCustomer'])
	->where('id', '[0-9]+');

	Route::get('/connect/{key}', ['uses' => 'CustomerController@connectByKey']);

	//post, put
	Route::post('/', ['uses' => 'CustomerController@store']);

	Route::put('/', ['uses' => 'CustomerController@update']);

});

/**
* Calls route
*/
Route::group(['prefix' => 'calls'], function(){

	//get
	Route::get('/', ['uses' => 'CallsController@allCalls']);

	Route::get('/{id}', ['uses' => 'CallsController@oneCalls'])
	->where('id', '[0-9]+');

	Route::get('/customer/{id}', ['uses' => 'CallsController@byCustomer'])
	->where('id', '[0-9]+');

	Route::get('/lastcall/customer/{id}', ['uses' => 'CallsController@lastCall'])
	->where('id', '[0-9]+');

	//post, put
	Route::post('/', ['uses' => 'CallsController@store']);

	Route::put('/', ['uses' => 'CallsController@update']);

	//delete
	Route::delete('/{id}', ['uses' => 'CallsController@destroy'])
	->where('id', '[0-9]+');
});

/**
* Orders route
*/
Route::group(['prefix' => 'orders'], function(){

	//get
	Route::get('/', ['uses' => 'OrderController@allOrder']);

	Route::get('/{id}', ['uses' => 'OrderController@oneOrder'])
	->where('id', '[0-9]+');

	Route::get('/customer/{id}', ['uses' => 'OrderController@forCustomer'])
	->where('id', '[0-9]+');

	Route::get('/cart/{id}', ['uses' => 'OrderController@showCart'])
	->where('id', '[0-9]+');

	Route::get('/toShip', ['uses' => 'OrderController@toShip']);

	Route::get('/ean/{id}', ['uses' => 'OrderController@withEan']);

	//post, put
	Route::post('/', ['uses' => 'OrderController@store']);

	Route::post('/cart', ['uses' => 'OrderController@storeCart']);

	Route::post('/submit', ['uses' => 'OrderController@storeCartSubmit']);

	Route::post('/delivery', ['uses' => 'OrderController@storeDelivery']);

	Route::post('/validate', ['uses' => 'OrderController@storeValidate']);

	Route::post('/shipped', ['uses' => 'OrderController@storeShipped']);

	Route::post('/paid', ['uses' => 'OrderController@storePaid']);

	Route::post('/rollback', ['uses' => 'OrderController@storeRollBack']);
});

/**
* Suppliers route
*/
Route::group(['prefix' => 'suppliers'], function(){

	//get
	Route::get('/{id}', ['uses' => 'SupplierController@oneSupplier'])
	->where('id', '[0-9]+');

	Route::get('/', ['uses' => 'SupplierController@allSupplier']);

	Route::get('/shippingorders', ['uses' => 'SupplierController@showShippingOrders']);

	Route::get('/ordercontent/{id}', ['uses' => 'SupplierController@showOrderContent'])
	->where('id', '[0-9]+');

	//put
	Route::put('/orders', ['uses' => 'SupplierController@updateOrderContent']);

	Route::put('/ordercontent', ['uses' => 'SupplierController@updateOrderContentForCheckin']);
});

/**
* Stock route
*/
Route::group(['prefix' => 'stocks'], function(){

	//get
	Route::get('/{id}', ['uses' => 'StockController@oneStock'])
	->where('id', '[0-9]+');

	Route::get('/tracker', ['uses' => 'StockController@tracker']);

	//post, put
	Route::put('/', ['uses' => 'StockController@update']);

	Route::put('/inventory', ['uses' => 'StockController@inventory']);
});