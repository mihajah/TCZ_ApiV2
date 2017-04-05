<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Device;

class BrandController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function allBrand()
	{
		//
		$display  = [];
		$allBrand = Device::wsAllBrand();
		foreach($allBrand as $one)
		{
			$display[] = ['id' => $one->id_value, 'name' => $one->value];
		}

		return $display;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getWithDevice()
	{
		//
		$device  = [];
		$allBrand = Device::wsAllBrand();
		foreach($allBrand as $brand)
		{
			if(Device::getAllDevice($brand->id_value))
				$device[$brand->value] = Device::getAllDevice($brand->id_value);
		}

		return $device;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getPopular()
	{
		//
		$brand  = [];
		$allBrand = Device::wsAllBrand();
		for($i=0; $i<count($allBrand) && $i < 8; $i++)
		{
			$brand[] = ['id' => $allBrand[$i]->id_value, 'name' => $allBrand[$i]->value];
		}

		return $brand;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
