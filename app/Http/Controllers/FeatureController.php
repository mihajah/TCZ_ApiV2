<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Property;

class FeatureController extends Controller {

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
	public function allFeature()
	{
		//
		return Property::set('feat')->getAll();
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
		if(!$verb->has('feature_name'))
		{
			return ['success' => FALSE, 'error' => 'You must provide feature_name'];
		}

		$data['feature_name'] 		= $verb->input('feature_name');

		if(!$verb->has('display_name'))
		{
			$data['display_name'] 	= $verb->input('feature_name');
		}
		else
		{
			$data['display_name'] 	= $verb->input('display_name');
		}

		$data['filter'] 			= 0;

		if($verb->input('filter') != '' && $verb->has('filter'))
		{
			$data['filter'] 		= $verb->input('filter');
		}

		$data['menu'] 				= 0;

		if($verb->input('menu') != '' && $verb->has('menu'))
		{
			$data['menu'] 			= $verb->input('menu');
		}

		$ins = Property::set('feat')->store($data);

		if($ins['success'])
		{
			if($verb->has('unit_test'))
			{
				$id = $ins['data']['id_feature'];
				Property::set('feat')->remove($id);
			}
			
			return $ins;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Feature already exist or invalid value'];
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
	public function update(Request $verb)
	{
		//
		if(!$verb->has('feature_name') || !$verb->input('id'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id, feature_name'];
		}

		$data['id'] 				= $verb->input('id');
		$data['feature_name'] 		= $verb->input('feature_name');

		if(!$verb->has('display_name'))
		{
			$data['display_name'] 	= $verb->input('feature_name');
		}
		else
		{
			$data['display_name'] 	= $verb->input('display_name');
		}

		$data['filter'] 			= 0;

		if($verb->input('filter') != '' && $verb->has('filter'))
		{
			$data['filter'] 		= $verb->input('filter');
		}

		$data['menu'] 				= 0;

		if($verb->input('menu') != '' && $verb->has('menu'))
		{
			$data['menu'] 			= $verb->input('menu');
		}

		if($verb->has('unit_test'))
		{
			return ['success' => TRUE];
		}

		$edit = Property::set('feat')->edit($data);

		if($edit['success'])
		{
			return $edit;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Invalid value'];
		}
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
