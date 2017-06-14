<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Order;

class OrderController extends Controller {

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
		return Order::wsAdd($verb);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeCart(Request $verb)
	{
		//
		return Order::wsAddCart($verb);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeCartSubmit(Request $verb)
	{
		//
		return Order::wsAddCartSubmit($verb);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeDelivery(Request $verb)
	{
		//
		return Order::wsAddDelivery($verb);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeValidate(Request $verb)
	{
		//
		return Order::wsAddValidate($verb);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeShipped(Request $verb)
	{
		//
		return Order::wsAddShipped($verb);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storePaid(Request $verb)
	{
		//
		return Order::wsAddPaid($verb);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeRollBack(Request $verb)
	{
		//
		return Order::wsAddRollBack($verb);
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
	public function oneOrder($id, Request $verb)
	{
		//
		$column = '';

		if($verb->has('column') && $verb->input('column') != '')
		{
			$column = $verb->input('column');
		}

		return Order::wsOne($id, $column);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function allOrder()
	{
		//
		return Order::wsAll();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function forCustomer($id)
	{
		//
		return Order::wsForCustomer($id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showCart($id)
	{
		//
		return Order::wsShowCart($id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function toShip()
	{
		//
		return Order::wsToShip();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function withEan($id)
	{
		//
		return Order::wsWithEan($id);
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
	public function updateToShip(Request $verb)
	{
		//
		if(!$verb->has('id') || !$verb->has('shipping_mode'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id, shipping_mode'];
		}

		$id                    = $verb->input('id');
		$data['shipping_mode'] = $verb->input('shipping_mode');

		if(!Order::find($id))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		Order::where('id_reseller_order', '=', $id)->update($data);
		return ['success' => TRUE, 'data' => Order::wsOne($id)];
	}

	public function updateChronopost(Request $verb)
	{
		//
		if(!$verb->has('id'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id'];
		}

		$fail     = FALSE;
		$order    = $verb->input('id');
		$data     = $verb->except('id');
		$editable = ['poids', 'largeur', 'longueur', 'hauteur'];

		foreach($editable as $key)
		{
			if(!$verb->has($key))
			{
				$fail = TRUE;
			}

			if($verb->input($key) === '')
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'You must provide required field with valid value', 'required field' => $editable];
		}

		if(!Order::find($order))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(Order::find($order)->delivery24 == 0)
		{
			return ['success' => FALSE, 'error' => 'Request only work with delivery24 > 0'];
		}

		Order::where('id_reseller_order', '=', $order)->update($data); //
		return ['success' => TRUE, 'data' => Order::wsOne($order)]; //
	}

	public function updateTotalCart(Request $verb)
	{
		//
		if(!$verb->has('id') || !$verb->has('total_cart'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id, total_cart'];
		}

		$order              = $verb->input('id');
		$data['total_cart'] = $verb->input('total_cart');

		if(!Order::find($order))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		Order::where('id_reseller_order', '=', $order)->update($data);
		return ['success' => TRUE, 'data' => Order::wsOne($order)];
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
