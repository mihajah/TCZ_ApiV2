<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Property;

class ColorController extends Controller {

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
	public function allColor()
	{
		//
		return Property::set('color')->getAll();
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
		$all 		= $verb->all();
		$fillable 	= ['name_fr', 'name_alt', 'name_eng', 'code', 'ref'];
		$fail 		= FALSE;

		if(count($all) != count($fillable))
		{
			return ['success' => FALSE, 'erro' => 'You must provide those column', 'column' => $fillable];
		}

		foreach($fillable as $key)
		{
			if(!$verb->has($key))
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'erro' => 'You must provide those column', 'column' => $fillable];
		}

		$ins = Property::set('color')->store($all);
		if($ins['success'])
		{
			return $ins;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Color already exist or invalid value'];
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
