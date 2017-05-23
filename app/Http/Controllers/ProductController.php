<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Prestashop as PS;
use App\Helpers\History;
use App\Product;
use App\Device;
use Route;

class ProductController extends Controller {

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
	public function allProduct()
	{
		return Product::allProducts();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id or $ean
	 * @return Response
	 */
	public function oneProduct($id_ean)
	{
		//
		if(!PS::product($id_ean))
		{
			return ['error' => 'product not found'];
		}

		$data = Product::wsOne($id_ean);
		return $data;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function allBrand()
	{
		$data = Product::wsAllBrand();
		return $data;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function productForDevice($device)
	{
		$data = Product::wsForDevice($device);
		return $data;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function amazone($id_product)
	{
		$data = Product::wsAmazone($id_product);
		return $data;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function historyLog($id_ean, $from = '', $to = '')
	{
		//
		History::_set_date_filter($from, $to);
		$id = PS::product($id_ean, 'PT.id_product');//ean -> id conversion
		return History::getLog($id);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function pManager($id = '')
	{
		if($id == '')
		{
			$o = [];
			$n = [];

			$link = Product::wsAll('custom');
			$obs = $link['link']->where($link['t1'].'.is_obsolete', '=', 1)
			->where($link['t2'].'.active', '=', 1)
			->orderBy($link['t1'].'.id_product', 'desc')
			->get();

			$link = Product::wsAll('custom');
			$not_obs = $link['link']->where($link['t1'].'.is_obsolete', '=', 0)
			->where($link['t1'].'.spicture1', '!=', 0)
			->orderBy($link['t1'].'.id_product', 'desc')
			->get();

			if(count($obs) > 0)
			{
				foreach($obs as $one_obs)
				{
					$o[] = $one_obs->id_product;
				}
			}

			if(count($not_obs) > 0)
			{
				foreach($not_obs as $one_nobs)
				{
					$n[] = $one_nobs->id_product;
				}
			}

			$id = array_merge($n, $o);

			return $id;
		}

		$current = Route::getCurrentRoute()->getPath();
		if(strpos($current, 'device') !== FALSE)
		{
			$data = Product::wsPmanager($id, 'device');
			return $data;
		}
		else if(strpos($current, 'box') !== FALSE)
		{
			return Product::getInBox($id);
		}
		else if(strpos($current, 'bigdata_brand') !== FALSE)
		{
			$all = Device::getAllDevice($id);
			return $all;
		}
		else
		{ 
			return Product::getFullSchema($id);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 * test for POST /products
	 * @return Response
	 */
	public function create()
	{
		//
		return view('product.test_add_product');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $verb)
	{
		//
		return Product::add($verb);
	}

	/**
	 * Send resource to specific people.
	 *
	 * @param  
	 * @return
	 */
	public function sendInfos(Request $verb)
	{
		$raw = $verb->all();
		if(!isset($raw['id_product']) || !isset($raw['mail']))
		{
			return ['success' => FALSE, 'error' => 'id_product, mail must set'];
		}

		return Product::sendInfos($raw);
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
		print_r(PS::product($id));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit()
	{
		//
		$data['edit'] = TRUE;
		return view('product.test_edit_product', $data);
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
		return Product::edit($verb);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function obsoletizer(Request $verb)
	{
		//
		if(!$verb->has('type'))
		{
			return ['success' => FALSE, 'error' => 'You must provide type'];
		}

		$type = $verb->input('type');
		$type = strtolower($type);
		$type = trim($type);

		if($type == 'obsolete')
		{
			$attr = 1;
		}
		else if($type == 'non obsolete')
		{
			$attr = 0;
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Type not allowed'];
		}

		if($verb->has('products') && !$verb->has('collectionID'))
		{
			$products = $verb->input('products');
		}
		else if(!$verb->has('products') && $verb->has('collectionID'))
		{
			$collection = $verb->input('collectionID');
			$products 	= Product::allProductInOnecollection($collection);
		}
		else
		{
			return ['success' => FALSE, 'error' => 'Operation undefined'];
		}

		if(count($products) > 0)
		{
			foreach($products as $one)
			{
				Product::where('id_product', '=', $one['id'])->update(['is_obsolete' => $attr]);
			}

			return ['success' => TRUE, 'error' => 'no error'];
		}
		else
		{
			return ['success' => FALSE, 'error' => 'No product found'];
		}
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function removeQuantity($product, Request $verb)
	{
		//
		if(!Product::find($product))
		{
			return ['success' => FALSE, 'error' => 'Product not found'];
		}

		if(!$verb->has('qty') || !$verb->input('qty'))
		{
			return ['success' => FALSE, 'error' => 'Quantity value must set'];
		}

		$quantity = (int) $verb->input('qty');
		return Product::removeQuantity($product, $quantity);
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
