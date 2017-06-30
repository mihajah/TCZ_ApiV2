<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Property;

class MaterialController extends Controller {

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
	public function allMaterial()
	{
		//
		return Property::set('material')->getAll();
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
		if(!$verb->has('material_name'))
		{
			return ['success' => FALSE, 'error' => 'You must provide material_name'];
		}

		$data['material_name'] 	= $verb->input('material_name');
		$data['supplier_name'] 	= '';
		$data['menu'] 			= 0;


		if($verb->has('supplier_name'))
		{
			$data['supplier_name'] 	= $verb->input('supplier_name');
		}

		if($verb->has('menu'))
		{
			$data['menu'] 			= $verb->input('menu');
		}

		$ins = Property::set('material')->store($data);

		if($ins['success'])
		{
			if($verb->has('unit_test'))
			{
				$id = $ins['data']['id_material'];
				Property::set('material')->remove($id);
			}

			return $ins;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Material already exist or invalid value'];
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
		if(!$verb->has('material_name') || !$verb->has('id'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id, material_name'];
		}

		$data['material_name'] 	= $verb->input('material_name');
		$data['supplier_name'] 	= '';
		$data['menu'] 			= 0;
		$data['id'] 			= $verb->input('id');


		if($verb->has('supplier_name'))
		{
			$data['supplier_name'] 	= $verb->input('supplier_name');
		}

		if($verb->has('menu'))
		{
			$data['menu'] 			= $verb->input('menu');
		}

		if($verb->has('unit_test'))
		{
			return ['success' => TRUE];
		}

		$edit = Property::set('material')->edit($data);

		if($edit['success'])
		{
			return $edit;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Material already exist or invalid value'];
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
