<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use App\Order;
use App\Product;
use App\Helpers\History;
use DB;

class Stock extends Model {

	protected $table 	  		 = 'ps_stock_available';
	protected $table_fordevice	 = 'apb_prd_fordevice';
	protected $primaryKey 		 = 'id_stock_available';
	protected static $product_pk = 'id_product';
	protected static $local_k	 = 'id_product';


	use ModelGetProperties;

	/**
	* ws Method
	*/
	public static function wsOne($id, $withProduct = FALSE) //id or ean
	{
		$p = Product::wsOne($id); //id/ean conversion

		if($withProduct)
		{
			
			$stock 				= self::get($p['id'], 'available');
			$real_stock 		= self::calculateRealStock($p['id']);
			$real_stock 		= $real_stock + $stock;
			$p['stock'] 		= self::get($p['id']);
			$p['stock_real'] 	= $real_stock;
			$product 			= $p;

			return $product;
		}		
		
		$stock = self::get($p['id']);
		return $stock;
	}

	public static function wsUpdate($verb)
	{
		//
		if(!$verb->has('id_product'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_product'];
		}

		$data['id_product'] 	= $verb->input('id_product');
		$data['qty_real'] 		= $verb->input('qty_real');
		$data['reason'] 		= $verb->input('reason');

		if($verb->has('unit_test'))
		{
			$data['unit_test']  = 'unit_test';
		}

		$res 					= self::updateStockTracking($data);

		if(isset($res['success']))
		{
			return $res;
		}

		if($res)
		{
			return ['success' => TRUE, 'error' => 'no error'];
		}
		else
		{
			return ['success' => FALSE, 'error' => 'DB fail'];
		}
	}

	public static function synch($verb)
	{

		$product 		= $verb->input('id_product');
		$stock_ph 		= $verb->input('sph');
		$stock_bdd 		= self::get($product, 'available');
		$isOrder 		= self::calculateRealStock($product);
		$stock_real 	= $isOrder + $stock_bdd;
		$product_log 	= History::getLog($product);
		$stock_theo 	= self::getLastStockFromLog($product_log);
		$change 		= 0;
		$change_theo 	= 0;

		if($stock_bdd != $stock_ph)
		{
			$stock_bdd_new = $stock_ph - $isOrder;
			if($stock_bdd > $stock_bdd_new)
			{
				$change = - ($stock_bdd - $stock_bdd_new);
			}
			else
			{
				$change = $stock_bdd_new - $stock_bdd;
			}

			//-- data
			$data['id_product'] 	= $product;
			$data['qty_from'] 		= $stock_bdd;
			$data['qty_real'] 		= $stock_bdd_new;
			$data['qty_change']  	= $change;
			$data['reason']  		= 4;

			if($verb->has('unit_test'))
			{
				$data['unit_test']  = 'unit_test';
			}
			//--

			$res = self::updateStockTracking($data, TRUE);
			if($res)
			{
				self::set($product, $stock_bdd_new);
			}	

			$data = '';
		}
		else
		{
			$stock_bdd_new = $stock_bdd;
		}

		if($stock_theo > 0)
		{
			if($change >= 0)
			{
				$stock_theo = $stock_theo - $change;
			}
			else
			{
				$stock_theo = $stock_theo + $change;
			}
		}
		else
		{
			$stock_theo  = - ($stock_theo);
			$change_theo = $stock_theo + $stock_bdd;
			$stock_theo  = $stock_bdd_new;			
		}

		if($stock_bdd_new != $stock_theo)
		{
			if($stock_bdd_new > $stock_theo)
			{
				if($stock_theo >= 0)
				{
					$change_theo = $stock_bdd_new - $stock_theo;
				}
				else
				{
					$change_theo = - ($stock_bdd_new + $stock_theo);
				}
			}
			else
			{
				$change_theo = - ($stock_theo - $stock_bdd_new);
			}

			$stock_theo = $stock_theo + $change_theo;

			if($stock_theo != $stock_bdd)
			{
				$stock_theo = $stock_bdd_new;
			}
			
		}

		//-- data
		$data['id_product'] 	= $product;
		$data['qty_from'] 		= $stock_bdd;
		$data['qty_real'] 		= $stock_bdd_new;
		$data['qty_change']  	= $change_theo;
		$data['reason']  		= 6;

		if($verb->has('unit_test'))
		{
			$data['unit_test']  = 'unit_test';
		}
		//--
		$res = self::updateStockTracking($data, TRUE); //short update, error: 6

		if(!$res)
		{
			return ['success' => TRUE];
		}

		$return = ['bdd' => $stock_bdd_new, 'change' => $change, 'theo' => $stock_theo, 'change_theo' => $change_theo];
		return json_encode(['synchronized' => $return]);
	}

	/**
	* Public Method
	*/
	protected static function getLastStockFromLog($log)
	{
		$arr_tmp = $log;
		if(count($arr_tmp) > 0)
		{
			$arr_tmp_index = [];
			foreach($arr_tmp as $k => $d)
			{
				$arr_tmp_index[] = $k;
			}

			ksort($arr_tmp_index);

			if(isset($arr_tmp_index[0]))
			{
				$index = $arr_tmp_index[0];
				if(count($arr_tmp[$index]) > 0)
				{
					$length = count($arr_tmp[$index]);
					$last_stock = $arr_tmp[$index][$length - 1];
					return $last_stock['data']['qty_real'];
				}
			}
		}
		else
		{
			return count($arr_tmp);
		}	
	} 

	public static function updateStockTracking($data, $short = FALSE)
	{	
		$table 		 =  'apb_stock_tracking';
		$reason_list = 	[
							1 => 'Produit d&eacute;fectueux (TT)', 
							2 => 'Produit d&eacute;fectueux (TZ)', 
							3 => 'Cadeau client', 
							4 => 'Erreur de stock', 
							5 => 'Erreur fournisseur',
							6 => 'Synchro stock BDD'
						];
						
		$reason 	 =  ['id' => $data['reason'], 'value' => $reason_list[$data['reason']]];

		if($short)
		{
			$value['id_product'] 	= $data['id_product'];
			$value['qty_change'] 	= $data['qty_change'];
			$value['qty_real']	 	= $data['qty_real'];
			$value['qty_from'] 		= $data['qty_from'];
			$value['reason'] 		= json_encode($reason);
			$value['date_updated'] 	= @date('Y-m-d H:i:s');
			
			if(isset($data['unit_test']))
			{
				$id = DB::table($table)->insertGetId($value);
				DB::table($table)->where('id', '=', $id)->delete();
				return FALSE;
			}

			return DB::table($table)->insert($value);
		}

		$stock_old 	= self::get($data['id_product'], 'available');
		$stock_new 	= $data['qty_real'];
		$prd 		= $data['id_product'];

		if($stock_old > $stock_new)
		{
			$qty = $stock_old - $stock_new;
			$qty = -$qty;
		}
		else if($stock_old < $stock_new)
		{
			$qty = $stock_new - $stock_old;
		}
		else
		{
			$qty = 0;
		}

		$value					= [];
		$value['id_product'] 	= $prd;
		$value['qty_change'] 	= $qty;
		$value['qty_real'] 		= $stock_new;
		$value['qty_from'] 		= $stock_old;
		$value['reason'] 		= json_encode($reason);
		$value['date_updated'] 	= @date('Y-m-d H:i:s');
		$res = DB::table($table)->insertGetId($value);

		if(isset($data['unit_test']))
		{
			DB::table($table)->where('id', '=', $res)->delete();
			return ['success' => $res];
		}

		if($res)
		{
			self::set($prd, $stock_new);
		}

		return $res;
	}

	public static function tracker($product_panel, $unit_test = '')
	{
		$table_log 	= 'apb_stock_log';
		$ins 		= 0;

		if($unit_test == 'unit_test')
		{
			$id_product 			= 3630;
			$stock 					= self::get($id_product, 'available');
			$data['id_product'] 	= $id_product;
			$data['stock'] 			= $stock;
			$data['date_logged'] 	= @date('Y-m-d H:i:s');

			$new = DB::table($table_log)->insertGetId($data);
			DB::table($table_log)->where('id', '=', $new)->delete();
			return ['logged' => $new];
		}

		if(count($product_panel) > 0)
		{
			foreach($product_panel as $pp)
			{
				$id_product 			= $pp;
				$stock 					= self::get($id_product, 'available');
				$data['id_product'] 	= $id_product;
				$data['stock'] 			= $stock;
				$data['date_logged'] 	= @date('Y-m-d H:i:s');

				$ins += DB::table($table_log)->insert($data);
			}
		}

		return ['logged' => $ins];
	}

	public static function get($product, $display = '')
	{
		$bdd      = self::getAvailable($product);
		$current  = self::calculateRealStock($product);
		$real 	  = $bdd + $current;
		$stock = ['available' => $bdd, 'real' => $real];

		if($display)
		{
			return $stock[$display];
		}

		return $stock; 
	}

	public static function set($product, $quantity)
	{
		return self::where('id_product', '=', $product)->update(['quantity' => $quantity]);
	}

	public static function fromBox($box)
	{
		$qty = 0;
		$many = Product::where('numbox', '=', $box)->get();
		if(count($many) > 0)
		{
			foreach($many as $p)
			{
				$qty += Stock::where('id_product', '=', $p->id_product)->first()->quantity;
			}
		}

		return $qty;
	}

	public static function linkForDevice()
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('table_fordevice');
		$link = self::join($table_two, $table_one.'.id_product', '=', $table_two.'.id_product');
		$data = ['link' => $link, 't1' => $table_one, 't2' => $table_two];
		return $data;
	}

	/**
	* Internal Method
	*/

	//--
	// know if product is really sold
	// techtablet status : 1 created, 2 validated, 3 sent, 4 done
	// touchiz status : table ps_order_state_lang
	// --
	protected static function calculateRealStock($product)
	{
		$ro 	= 'apb_reseller_orders';
		$rc 	= 'apb_reseller_carts';
		$po 	= 'ps_orders';
		$pcp 	= 'ps_cart_product';

		$real = 0;

		$result = DB::table($ro)->join($rc, $ro.'.id_reseller_order', '=', $rc.'.id_reseller_order')
		->select($rc.'.quantity AS QT')
		->where($rc.'.id_product', '=', $product)
		->where($ro.'.status', '<', 3)
		->get();

		if(count($result) > 0)
		{
			foreach($result as $qt)
			{
				$real += $qt->QT;
			}
		}

		$result = DB::table($po)->join($pcp, $po.'.id_cart', '=', $pcp.'.id_cart')
		->select($pcp.'.quantity AS QT')
		->where($pcp.'.id_product', '=', $product)
		->whereIn($po.'.current_state', [1, 2, 3, 14, 15])
		->get();

		if(count($result) > 0)
		{
			foreach($result as $qt)
			{
				$real += $qt->QT;
			}
		}

		return $real;
	}

	protected static function getAvailable($product)
	{
		$stock = 0;
		$results = self::where('id_product', '=', $product)->get();
		if($results->count() > 0)
		{
			foreach($results as $q)
			{
				$stock += $q->quantity;
			}
		}			

		return $stock;
	}

}


