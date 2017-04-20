<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;
use App\Customer;

class Order extends Model {

	//
	protected $table 				= 'apb_reseller_orders';
	protected $primaryKey 			= 'id_reseller_order';
	protected $order_apb_table 		= 'apb_reseller_carts';
	protected $order_ps_table 		= 'ps_orders';
	protected $order_ps_cart_table 	= 'ps_cart_product';
	protected $customer_table		= 'apb_customers';
	protected $table_tmp_cart		= 'apb_reseller_tmp_carts';

	use ModelGetProperties;

	/**
	* ws Method
	*/
	public static function wsOne($id)
	{
		return self::getFullSchema($id, FALSE);
	}

	public static function wsAll()
	{
		$id = [];
		$result = self::getAllId(FALSE);
		if(count($result) == 0)
		{
			return $id;
		}

		foreach($result as $one)
		{
			$id[] = self::getFullSchema($one->id_reseller_order, FALSE);
		}

		return $id;
	}

	public static function wsForCustomer($id)
	{
		$fc = [];
		$order = self::getForCustomerId($id, FALSE);
		if(count($order) == 0)
		{
			return $fc;
		}

		foreach($order as $one)
		{
			$fc[] = self::getFullSchema($one, FALSE);
		}

		return $fc;
	}

	public static function wsShowCart($id)
	{
		return self::getTmpCart($id, FALSE);
	}


	/**
	* Public Method
	*/
	public static function getTmpCart($order, $staging)
	{
		$tmp = [];
		$sql = "SELECT id_product,quantity 
				FROM ".self::getProp('table_tmp_cart').($staging?"_staging":"")." 
				WHERE `id_reseller_order`=".$order;

		$result = DB::select($sql);
		if(count($result) == 0)
		{
			return [];
		}

		foreach($result as $one)
		{
			$tmp[] = ['id_product' => $one->id_product, 'quantity' => $one->quantity];
		}

		return $tmp;
	}

	public static function getForCustomerId($id, $staging)
	{
		$order = [];
		$sql = "SELECT O.id_reseller_order 
				FROM ".self::getProp('table').($staging?"_staging":"")." 
				as O WHERE id_customer=".$id." 
				ORDER BY O.id_reseller_order DESC";

		$result = DB::select($sql);
		if(count($result) == 0)
		{
			return $order;
		}

		foreach($result as $one)
		{
			$order[] = $one->id_reseller_order;
		}
		
		return $order;
	}

	public static function getAllId($staging = FALSE)
	{
		if($staging)
		{
			$result = DB::table(self::getProp('table_staging'))->orderBy('id_reseller_order', 'desc')->get();
		}
		else
		{
			$result = self::all()->sortByDesc('id_reseller_order');
		}

		return $result;
	}

	public static function getCurrentStock($id_product)
	{
		$ro   = self::getProp('table');
		$rc   = self::getProp('order_apb_table');
		$po   = self::getProp('order_ps_table');
		$pcp  = self::getProp('order_ps_cart_table');
		$real = 0;

		$link = self::join($rc, $ro.'.id_reseller_order', '=', $rc.'.id_reseller_order')
		->select($rc.'.quantity AS QT')
		->where($rc.'.id_product', '=', $id_product)
		->where($ro.'.status', '<', 3)
		->get();

		if(count($link) > 0)
		{
			foreach($link as $row)
			{
				$real += $row->QT;
			}
		}

		$link = DB::table($po)->join($pcp, $po.'.id_cart', '=', $pcp.'.id_cart')
		->select($pcp.'.quantity AS QT')
		->where($pcp.'.id_product', '=', $id_product)
		->where($po.'.current_state', '<', 4)
		->get();

		if(count($link) > 0)
		{
			foreach($link as $row)
			{
				$real += $row->QT;
			}
		}

		return $real;
	}

	public static function getSold($id, $time = '', $type = [])
	{
		$pcp  = self::getProp('order_ps_cart_table');
		$po   = self::getProp('order_ps_table');
		$rc   = self::getProp('order_apb_table');
		$ro   = self::getProp('table');

		if(!empty($type))
		{
			if($type['side'] == 'touchiz')
			{
				$date = $type['date'];
				if(!$date)
				{
					$sql = "SELECT SUM( C.quantity ) AS totalNumber
							FROM ".$pcp." AS C
							LEFT JOIN ".$po." AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state = 4 OR O.current_state = 5 )";
				}
				else
				{
					$sql = "SELECT SUM( C.quantity ) AS totalNumber
							FROM ".$pcp." AS C
							LEFT JOIN ".$po." AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state = 4 OR O.current_state = 5 ) AND O.date_add>'".$date."'";
				}

				$results = DB::select($sql);	
				if(count($results) > 0)
				{
					return ''.$results[0]->totalNumber;
				}
				else
				{
					return 0;
				}	
			}

			if($type['side'] === 'techtablet')
			{
				$date = $type['date'];
				if(!$date)
				{
					$sql = "SELECT SUM(C.quantity) AS totalNumber 
							FROM ".$rc." AS C
							INNER JOIN ".$ro." AS O ON O.id_reseller_order = C.id_reseller_order
							WHERE C.id_product='".$id."' AND O.status > 3";
				}
				else
				{
					$sql = "SELECT SUM(C.quantity) AS totalNumber 
							FROM ".$rc." AS C
							INner JOIN ".$ro." AS O ON O.id_reseller_order = C.id_reseller_order
							WHERE C.id_product='".$id."' AND O.status > 3  AND O.billing_date>'".$date."'";
				}

				$results = DB::select($sql);	
				if(count($results) > 0)
				{
					return ''.$results[0]->totalNumber;
				}
				else
				{
					return 0;
				}
			}
		}
		else
		{
			$sql = 'SELECT '.$pcp.'.id_product, '.$pcp.'.quantity
					FROM '.$pcp.' 
					INNER JOIN '.$po.' ON '.$po.'.id_cart = '.$pcp.'.id_cart 
					AND '.$po.'.valid = 1
					WHERE '.$pcp.'.id_product = '.$id.' 
					AND ('.$po.'.date_add BETWEEN DATE_SUB(NOW(), INTERVAL '.$time.' DAY) 
					AND NOW())';
			$results = DB::select($sql);

			$qty = 0;
			if(count($results > 0))
			{
				foreach($results as $q)
				{
					$qty += $q->quantity;
				}
			}

			return ['value' => $qty, 'display' => $qty];
		}			
	}

	public static function getFullSchema($id, $staging, $display = 'both')
	{
		$full = self::remapOrderAttributes($id, $staging, 'arr');

		if($display == 'both')
		{
			return $full;
		}

		if($display == 'key')
		{
			$key = [];
			foreach($full as $k => $v)
			{
				$key[] = $k;
			}
			
			return $key;
		}

		if($display == 'value')
		{
			$value = [];
			foreach($full as $k => $v)
			{
				$value[] = $v;
			}

			return $value;
		}
	}

	public static function getCart($id, $staging = FALSE)
	{
		$cart = [];
		$sql = "SELECT * FROM ".self::getProp('order_apb_table').($staging?"_staging":"")." WHERE `id_reseller_order`= ".$id;
		$result = DB::select($sql);
		if(count($result) == 0)
		{
			return $cart;
		}

		foreach($result as $c)
		{
			$cart['format_one'] = [$c->id_product => $c->quantity];
			$cart['format_two']	= ['produit' => $c->id_product, 'qty' => $c->quantity];
		}

		return $cart;
	}

	/**
	* Internal Method
	*/
	protected static function remapOrderAttributes($id, $staging = FALSE, $vmode = 'obj')
	{
		$order = [];
		$sql = "SELECT O.*,C.name AS customer_name FROM ".self::getProp('table').($staging?"_staging":"")." as O
				LEFT JOIN ".self::getProp('customer_table')." AS C ON C.id_customer = O.id_customer  
				WHERE id_reseller_order = ".$id;

		$result = DB::select($sql);

		if(count($result) == 0)
		{
			return $order;
		}

		$row = $result[0];

		$order['id'] 				= $row->id_reseller_order;
		$order['status'] 			= $row->status;
		$order['customer']			= ['id' => $row->id_customer, 'name' => $row->customer_name];
		$order['discount']			= $row->discount;
		$order['billing_number']	= $row->unique_id;
		$order['billing_date']		= $row->billing_date;
		$order['shipping_fee']		= $row->shipping_fee;
		$order['delivery24']		= $row->delivery24;
		$order['payment_method']	= $row->payment_method;
		$order['lastupdate_date']	= $row->lastupdate_date;
		$order['fake']				= $staging;
		$cart 						= self::getCart($row->id_reseller_order);
		$order['cart']				= (isset($cart['format_one'])) ? $cart['format_one'] : $cart;
		$order['cart2']				= (isset($cart['format_two'])) ? $cart['format_two'] : $cart;
		$order['transaction']		= 0;
		$order['transaction_date']	= '';

		if($vmode == 'obj')
			return (object) $order;

		return $order;
	}
}
