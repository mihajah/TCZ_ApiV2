<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Reliquat;

class ReliquatController extends Controller {

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
	public function oneReliquat($id)
	{
		//
		return Reliquat::wsOne($id);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function allReliquat()
	{
		//
		return Reliquat::wsAll();
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function byCustomer($id)
	{
		//
		return Reliquat::wsByCustomer($id);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function byOrder($id)
	{
		//
		return Reliquat::wsByOrder($id);
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
		$all      = $verb->all();
		$fillable = ['id_customer', 'id_order', 'id_product', 'qty_initial', 'qty_sent', 'qty_left'];
		$fail     = FALSE;

		if(count($all) != count($fillable))
		{
			$fail = TRUE;
		}

		foreach($all as $k => $v)
		{
			if(!in_array($k, $fillable))
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'You must provide required field', 'Required field' => $fillable, 'Your field' => $all];
		}

		$data  = $verb->except('unit_test');
		$exist = Reliquat::beforeSave($data);

		if($exist['success'])
		{
			return ['success' => TRUE, 'data' => Reliquat::wsOne($exist['data'])]; 
		}
		else
		{
			return Reliquat::wsAdd($data);
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
