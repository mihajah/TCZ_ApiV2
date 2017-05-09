<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Property;

class PatternController extends Controller {

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
	public function allPattern()
	{
		//
		return Property::set('pattern')->getAll();
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
		if(!$verb->has('pattern_name'))
		{
			return ['success' => FALSE, 'error' => 'You must provide pattern_name'];
		}

		$data['pattern_name'] 		= $verb->input('pattern_name');
		$data['supplier_name'] 		= '';
		$data['menu'] 				= 0;

		if($verb->has('supplier_name') && $verb->input('supplier_name') != '')
		{
			$data['supplier_name'] 	= $verb->input('supplier_name');
		}

		if($verb->has('menu') && $verb->input('menu') != '')
		{
			$data['menu'] 			= $verb->input('menu');
		}

		$ins = Property::set('pattern')->store($data);

		if($ins['success'])
		{
			return $ins;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Pattern already exist or invalid value'];
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
		if(!$verb->has('pattern_name') || !$verb->has('id'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id, pattern_name'];
		}

		$data['pattern_name'] 		= $verb->input('pattern_name');
		$data['id'] 				= $verb->input('id');
		$data['supplier_name'] 		= '';
		$data['menu'] 				= 0;

		if($verb->has('supplier_name') && $verb->input('supplier_name') != '')
		{
			$data['supplier_name'] 	= $verb->input('supplier_name');
		}

		if($verb->has('menu') && $verb->input('menu') != '')
		{
			$data['menu'] 			= $verb->input('menu');
		}

		$edit = Property::set('pattern')->edit($data);

		if($edit['success'])
		{
			return $edit;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Pattern already exist or invalid value'];
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
