<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;
use App\Product;
use App\Order;


class Supplier extends Model {

	//
	protected $table 			= 'apb_suppliers';
	protected $primaryKey 		= 'id_supplier';
	protected $table_order		= 'apb_orders';
	protected $table_prd_order 	= 'apb_prd_order';
	protected $table_prd 		= 'apb_prd';

	use ModelGetProperties;

	/**
	*ws Method
	*/
	public static function wsOne($id)
	{
		//
		if(!self::find($id))
		{
			return [];
		}

		$one = self::find($id);
		return  [
					'id' 	=> $one->id_supplier,
					'name'	=> $one->supplier_name,
					'key'	=> $one->key
				];
	}

	public static function wsAll()
	{
		//
		$all 	= [];
		$result = self::all();
		if(count($result)  == 0)
		{
			return $all;
		}

		foreach($result as $one)
		{
			$all[] = [
						'id' 	=> $one->id_supplier,
						'name'	=> $one->supplier_name,
						'key'	=> $one->key
					 ];
		}

		return $all;
	}

	public static function wsShippingOrders()
	{
		$list = [];
		$supplier = self::getAllShippingOrders();
		if(count($supplier) == 0)
		{
			return $list;
		}

		foreach($supplier as $one)
		{
			$attr			= ['id' => $one->id_supplier, 'name' => $one->supplier_name];
			$item_number 	= DB::table(self::getProp('table_prd_order'))->where('id_order', '=', $one->id_order)->count();
			$list[] 		= [
									'id' 			=> $one->id_order,
									'status' 		=> $one->step,
									'supplier' 		=> $attr,
									'item_number'	=> $item_number
							  ];
		}

		return $list;
	}

	public static function wsOrderContent($id)
	{
		$order = [];
		$result = self::getOrdersDetail($id);
		if(count($result) == 0)
		{
			return $order;
		}

		foreach($result as $one)
		{
			$attr = Product::wsOne($one->id_product);			
			$order[] = 	[
							'id' 			=> $one->id_product,
							'buying_price' 	=> $one->buying_price,
							'parcel_number' => $one->parcel_number,
							'weight' 		=> $one->weight, 
							'qty_wanted' 	=> $one->qty_wanted, 
							'qty_shipped' 	=> $one->qty_shipped, 
							'qty_received' 	=> $one->qty_received,
							'about_prd'		=> $attr
						];
		}

		return $order;
	}

	public static function wsEditOrderContent($verb)
	{
		//
		if(!$verb->has('id_product') || !$verb->has('id_supplier') || !$verb->has('qtty'))
		{
			return ['success' => FALSE, 'error' => 'id_product, id_supplier, qtty must be provided'];
		}

		$id_product 	= $verb->input('id_product');
		$id_supplier 	= $verb->input('id_supplier');
		$qtty 			= $verb->input('qtty');

		if($qtty < 0)
		{
			return ['success' => FALSE, 'error' => 'Quantity must be > 0'];
		}

		return self::updateSupplierOrder($id_product, $id_supplier, $qtty);
	}

	/**
	* Public Method
	*/
	public static function updateSupplierOrder($p, $s, $q)
	{
		//
		Product::where('id_product', '=', $p)->update(['orderedqtty' => $q]);
		$field 		= ['height', 'width', 'depth', 'weight'];
		$pi 		= Product::getFullSchema($p, $field);
		$height 	= $pi['height']['value'];
		$width 		= $pi['width']['value'];
		$depth 		= $pi['depth']['value'];
		$weight 	= $pi['weight']['value'];

		$isOrder 	= FALSE;
		$today 		= @date('Y-m-d H:i:s'); 

		$result = DB::table(self::getProp('table_order'))->where('id_supplier', '=', $s)->where('step', '<', 3)->get();
		if(count($result) > 0)
		{
			$isOrder = TRUE;
			foreach($result as $one)
			{
				$availableOrder = $one->id_order;
			}
		}

		if($isOrder)
		{
			$n = DB::table(self::getProp('table_prd_order'))
			->where('id_product', '=', $p)
			->where('id_order', '=', $availableOrder)
			->count();

			if($n > 0)
			{
				if($q != 0)
				{
					$data['qty_wanted'] = $q;
					$data['height'] 	= $height;
					$data['width'] 		= $width;
					$data['depth'] 		= $depth;
					$data['weight'] 	= $weight;

					DB::table(self::getProp('table_prd_order'))
					->where('id_order', '=', $availableOrder)
					->where('id_product', '=', $p)
					->update($data);
				}
				else
				{
					DB::table(self::getProp('table_prd_order'))
					->where('id_order', '=', $availableOrder)
					->where('id_product', '=', $p)
					->delete();
				}				
			}	
			else
			{
				if($q != 0)
				{
					$data['qty_wanted'] = $q;
					$data['height'] 	= $height;
					$data['width'] 		= $width;
					$data['depth'] 		= $depth;
					$data['weight'] 	= $weight;
					$data['id_order']	= $availableOrder;
					$data['id_product']	= $p;

					DB::table(self::getProp('table_prd_order'))->insert($data);
				}
			}

			DB::table(self::getProp('table_order'))
			->where('id_order', '=', $availableOrder)
			->update(['date_updated' => $today]);
		}
		else
		{
			$data['id_supplier']	= $s;
			$data['step']			= 1;
			$data['date_added']		= $today;
			$data['date_updated']	= $today;

			$last_order = DB::table(self::getProp('table_order'))->insertGetId($data);
			$data = [];
			if($last_order)
			{
				$data['qty_wanted'] = $q;
				$data['height'] 	= $height;
				$data['width'] 		= $width;
				$data['depth'] 		= $depth;
				$data['weight'] 	= $weight;
				$data['id_order']	= $last_order;
				$data['id_product']	= $p;

				DB::table(self::getProp('table_prd_order'))->insert($data);
			}
		}

		return ['success' => TRUE, 'error' => 'updated with no error'];
	}

	public static function getOrdersDetail($id)
	{
		$to = self::getProp('table_prd');
		$tt = self::getProp('table_prd_order');

		$result = DB::table($to)->join($tt, $to.'.id_product', '=', $tt.'.id_product')
		->where($tt.'.id_order', '=', $id)
		->orderBy($to.'.numbox', 'asc')
		->get();

		return $result;
	}

	public static function getAllShippingOrders()
	{
		$to = self::getProp('table');
		$tt = self::getProp('table_order');

		$result = self::join($tt, $to.'.id_supplier', '=', $tt.'.id_supplier')
		->where($tt.'.step', '=', 5)
		->orderBy($tt.'.id_order', 'desc')
		->get();

		return $result;
	}


	/**
	* Internal Method
	*/
}
