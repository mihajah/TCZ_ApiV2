<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Stock;

class StockController extends Controller {

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
	public function oneStock($id)
	{
		//
		return Stock::wsOne($id);
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
	public function update(Request $verb)
	{
		//
		return Stock::wsUpdate($verb);
	}

	/**
	 * Watch the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function tracker()
	{
		//
		include '/var/www/public_html/stock_tracker/product_panel.php';
		return Stock::tracker($product_panel);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function inventory(Request $verb)
	{
		//
		if(!$verb->has('id_product') || !$verb->has('sph'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_product, sph'];
		}

		return Stock::synch($verb);
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
