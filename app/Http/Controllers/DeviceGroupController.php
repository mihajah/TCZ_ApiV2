<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Property;

class DeviceGroupController extends Controller {

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
	public function store(Request $verb)
	{
		//
		$groupName 	= $verb->input('name');
		$groupId 	= $verb->input('brandID');

		if(!$verb->has('id'))
		{
			if($groupName != '')
			{
				$groupList = Property::set('deviceGroup')->getAll();
				if(count($groupList) > 0)
				{
					foreach($groupList as $one)
					{
						if($one->name == $groupName)
						{
							$success = FALSE;
						}
						else
						{
							$success = TRUE;
						}
					}					
				}
				else
				{
					$success = FALSE;
				}
			}
			else
			{
				$success = FALSE;
			}

			if($success)
			{
				$data['group_name'] 		= $groupName;
				$data['id_brand'] 			= $groupId;
				$data['group_popularity'] 	= 0;
				$ins = Property::set('deviceGroup')->store($data);
				return ['success' => $ins];
			}
			else
			{
				return ['success' => FALSE];
			}
		}	
		else
		{
			$id = $verb->input('id');
			if(!$verb->has('name') || !$verb->has('brandID'))
			{
				return ['success' => FALSE];
			}
			else
			{
				$data 				= [];
				$data['group_name'] = $groupName;
				$data['id_brand'] 	= $groupId;
				$data['id_group'] 	= $id;
				$upd = Property::set('deviceGroup')->edit($data);
				return  ['success' => $upd];
			}
		}
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
