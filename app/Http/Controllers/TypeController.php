<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Property;

class TypeController extends Controller {

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
	public function allType()
	{
		//
		return Property::set('type')->getAll();
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
		$fail 		= FALSE;
		$fillable 	= ['type_name', 'type_alt', 'type_display', 'type_title', 'html_name', 'type_desc', 'type_one', 'type_filter'];

		if(count($all) != count($fillable))
		{
			return ['success' => FALSE, 'error' => 'You must provide those column', 'column' => $fillable];
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
			return ['success' => FALSE, 'error' => 'You must provide those column', 'column' => $fillable];
		}

		$ins = Property::set('type')->store($all);

		if($ins['success'])
		{
			return $ins;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Type already exist or invalid value'];
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
		$all 		= $verb->all();
		$fail 		= FALSE;
		$fillable 	= ['id', 'type_name', 'type_alt', 'type_display', 'type_title', 'html_name', 'type_desc', 'type_one', 'type_filter'];

		if(count($all) != count($fillable))
		{
			return ['success' => FALSE, 'error' => 'You must provide those column', 'column' => $fillable];
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
			return ['success' => FALSE, 'error' => 'You must provide those column', 'column' => $fillable];
		}

		$edit = Property::set('type')->edit($all);

		if($edit['success'])
		{
			return $edit;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Type already exist or invalid value'];
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
