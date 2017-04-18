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
