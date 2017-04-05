<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;

class Order extends Model {

	//
	protected $table 				= 'apb_reseller_orders';
	protected $primaryKey 			= 'id_reseller_order';
	protected $order_apb_table 		= 'apb_reseller_carts';
	protected $order_ps_table 		= 'ps_orders';
	protected $order_ps_cart_table 	= 'ps_cart_product';

	use ModelGetProperties;

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
}
