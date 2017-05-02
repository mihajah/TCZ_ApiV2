<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Supplier;

class SupplierController extends Controller {

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
	public function oneSupplier($id)
	{
		//
		return Supplier::wsOne($id);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function allSupplier()
	{
		//
		return Supplier::wsAll();
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
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showShippingOrders() 
	{
		//
		return Supplier::wsShippingOrders();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showOrderContent($id) 
	{
		//
		return Supplier::wsOrderContent($id);
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
	public function updateOrderContent(Request $verb)
	{
		//
		return Supplier::wsEditOrderContent($verb);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Request
	 * @return Response
	 * used by http://staging.touchiz.fr/dev/checkin/
	 */
	public function updateOrderContentForCheckin(Request $verb) 
	{
		//
		if(!$verb->has('id_product') || !$verb->has('id_order'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_product, id_order'];
		}

		$data['id_product'] 	= $verb->input('id_product');
		$data['id_order'] 		= $verb->input('id_order');
		$data['qr'] 			= $verb->input('qr');
		$data['unk'] 			= $verb->input('unk');

		//todo : store data in DB using model
		//eg : Supplier::ws.....

		return ['success' => TRUE, 'error' => ''];
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
