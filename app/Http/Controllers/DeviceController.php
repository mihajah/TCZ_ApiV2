<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Device;
use Route;

class DeviceController extends Controller {

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
	public function allDevice()
	{
		//
		$allDevice = Device::wsAll();
		return $allDevice;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function oneDevice($id)
	{
		//
		if(!Device::find($id))
		{
			return ['error' => 'device not found'];
		}

		$aDevice = Device::wsOne($id);
		return $aDevice;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function allDeviceByBrand($id, $ignore = '')
	{
		//
		$device = Device::wsAllByBrand($id, '', $ignore);
		return $device;
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
	public function allDeviceByType($id, $ignore = '')
	{
		//
		$current = Route::getCurrentRoute()->getPath();
		if(strpos($current, 'phone') !== FALSE)
		{
			$device = Device::wsAllByBrand($id, 11, $ignore);
			return $device;
		}

		if(strpos($current, 'tablet') !== FALSE)
		{
			$device = Device::wsAllByBrand($id, 2, $ignore);
			return $device;
		}		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $verb)
	{
		//
		$state = Device::wsAdd($verb);

		if(isset($state['empty']) && $state['empty'])
		{
			return ['success' => FALSE, 'error' => 'The following column can\'t be empty', 'column' => $state['error']];
		}

		if($state['success'])
		{
			return $state['new_data'];
		}
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
	public function edit(Request $verb)
	{
		//
		if(!$verb->input('id_device'))
		{
			return ['success' => FALSE, 'error' => 'id_device must set'];
		}	

		$did = $verb->input('id_device');
		if(!Device::find($did))
		{
			return ['success' => FALSE, 'error' => 'Device not found'];
		}

		$state = Device::wsEdit($verb);
		if(is_array($state))
		{
			return $state;
		}
		else
		{
			return ['success' => TRUE, 'error' => ''];
		}
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
