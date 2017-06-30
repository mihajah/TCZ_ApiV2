<?php
namespace App\Helpers;
use DB;
use App\Helpers\Prestashop as PS;


class History
{
	protected static $event 	=   [
										1 => ['label' => 'achat chez le fournisseur', 'table' => ''],
										2 => ['label' => 'vente sur touchiz.fr', 'table' => ''],
										3 => ['label' => 'vente sur techtablet.fr', 'table' => ''],
										4 => ['label' => 'modification manuelle des stocks', 'table' => 'apb_stock_tracking']
									];

	protected static $default 	= 	[
										0 => ['id' => null]
									];

	protected static $limit 	= FALSE;
	protected static $from;
	protected static $to;

	public static function _set_date_filter($from, $to)
	{
		self::$from = $from;
		self::$to 	= $to;
	}

	public static function getLog($id_ean)
	{
		if($id_ean == '')
		{
			$return = ['error' => 'Valid product ID required'];
			return $return;
		}

		if(strlen($id_ean) == 12 || strlen($id_ean) == 13)
		{
			$id_ean = PS::product($id_ean, 'PT.id_product');
		}

		$id = $id_ean;
		$log = self::_prepare_data($id);

		return $log;
	}

	protected static function _prepare_data($product)
	{
		$loop  		= self::$event;
		$data  		= [];
		$sort_asc 	= [];
		$sort_desc 	= [];
		$qty 		= 0;


		foreach($loop as $k => $v)
		{
			$data[] = self::_raw_log($k, $product);
		}

		foreach($data as $tosort)
		{
			foreach($tosort as $k => $row)
			{
				if(isset($row['date']) && $row['date'] != ''):
						$sort_asc[$row['dateFull']][] = ['data' => [
														'date' 			=> $row['date'],
														'dateFull'		=> $row['dateFull'],
														'description' 	=> $row['description'],
														'quantity' 		=> $row['qty_change'],
														'id_product' 	=> $row['id_product'],
														'id_order'		=> $row['id_order'],
														'event' 		=> $row['event'],
														'qty_real' 		=> $row['qty_real']
													  ]];	
				endif;
			}
		}

		ksort($sort_asc);

		foreach($sort_asc as $date => $rdata)
		{
			foreach($rdata as $tmp)
			{
				$row 		= $tmp['data'];
				$event  	= $row['event'];
				$qty_change = $row['quantity'];

				if($event == 2 || $event == 3)
				{
					$qty -= $qty_change;
				}

				if($event == 1)
				{
					$qty += $qty_change;
				}

				if($event == 4)
				{
					if($qty_change < 0)
					{
						$qty -= (-1 * $qty_change);
					}
					else if($qty_change >= 0)
					{
						$qty += $qty_change;
					}
				}

				$sort_desc[$date][] = ['data' => [
													'date' 			=> $row['date'],
													'dateFull'		=> $row['dateFull'],
													'description' 	=> $row['description'],
													'quantity' 		=> $qty_change,
													'id_product' 	=> $row['id_product'],
													'id_order'		=> $row['id_order'],
													'event' 		=> $row['event'],
													'qty_real' 		=> $qty
												]];
			}
		}
			

		krsort($sort_desc);
		return $sort_desc;
	}

	protected static function _raw_log($e, $product)
	{
		switch ($e)
		{
			case 1:
				
				$sql = "SELECT O.id_order,(CP.qty_ok+CP.qty_knok) AS quantity, DATE_FORMAT(O.date_updated,'%Y-%m-%d') AS date_upd,
						O.date_updated AS dateFull
						FROM `apb_prd_order` as CP
						LEFT JOIN `apb_orders` as O ON O.id_order = CP.id_order
						WHERE CP.id_product = ".$product." AND (O.processed = 1)
						AND date_updated != '0000-00-00 00:00:00'							
						";

				if(self::$limit)
				{
					if(self::$from == '' && self::$to == '')
					{
						$sql .= " AND O.date_updated > (CURRENT_DATE - INTERVAL 3 MONTH)";
					}
				}				

				if(self::$from != '' && self::$to == '')
				{
					$sql .= " AND O.date_updated LIKE '%".self::$from."%'";
				}
				
				if(self::$from != '' && self::$to != '')
				{
					$sql .= " AND O.date_updated >= '".self::$from."' AND O.date_updated <= '".self::$to."'";
				}

				$sql .=	" ORDER BY O.date_updated DESC";

				$res = DB::select($sql);
				if(count($res) == 0)
				{
					return self::$default;
					exit;
				}

				$i = 0;
				foreach($res as $row)
				{
						$data['id_product'] 	= $product;
						$data['date'] 			= $row->date_upd;
						$data['dateFull']		= $row->dateFull;
						$data['description'] 	= ['id' => 1, 'value' => 'achat chez le fournisseur'];
						$data['qty_change'] 	= $row->quantity;
						$data['qty_real']		= '';
						$data['event'] 			= $e;
						$data['id_order']		= $row->id_order;

					$fdata[$i] = $data;
					$i++;
				}

				return $fdata;

			break;

			case 2:
				
				$sql = "SELECT O.id_order,CP.quantity,DATE_FORMAT(O.date_upd,'%Y-%m-%d') AS date_upd,
						O.date_upd AS dateFull
						FROM `ps_cart_product` as CP
						LEFT JOIN `ps_orders` as O ON O.id_cart = CP.id_cart
						WHERE CP.id_product = ".$product." AND (O.current_state IS NOT NULL)
						AND date_upd != '0000-00-00 00:00:00'
						";


				if(self::$limit)
				{
					if(self::$from == '' && self::$to == '')
					{
						$sql .= " AND O.date_upd > (CURRENT_DATE - INTERVAL 3 MONTH)";
					}
				}				

				if(self::$from != '' && self::$to == '')
				{
					$sql .= " AND O.date_upd LIKE '%".self::$from."%'";
				}
				
				if(self::$from != '' && self::$to != '')
				{
					$sql .= " AND O.date_upd >= '".self::$from."' AND O.date_upd <= '".self::$to."'";
				}				


				$sql .=	" ORDER BY O.date_upd DESC";


				$res = DB::select($sql);
				if(count($res) == 0)
				{
					return self::$default;
					exit;
				}

				$i = 0;
				foreach($res as $row)
				{
						$data['id_product'] 	= $product;
						$data['date'] 			= $row->date_upd;
						$data['dateFull']		= $row->dateFull;
						$data['description'] 	= ['id' => 1, 'value' => 'vente sur touchiz.fr'];
						$data['qty_change'] 	= $row->quantity;
						$data['qty_real']		= '';
						$data['event'] 			= $e;
						$data['id_order']		= $row->id_order;

					$fdata[$i] = $data;
					$i++;
				}

				return $fdata;

			break;

			case 3:
				
				$sql = "SELECT C.id_reseller_order,C.quantity,DATE_FORMAT(O.billing_date,'%Y-%m-%d') AS billing_date,
						O.billing_date AS dateFull
						FROM `apb_reseller_carts` as C
						LEFT JOIN `apb_reseller_orders` as O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product = ".$product." AND (O.status > 1)			
						AND billing_date != '0000-00-00 00:00:00'		
						";

				if(self::$limit)
				{
					if(self::$from == '' && self::$to == '')
					{
						$sql .= " AND O.billing_date > (CURRENT_DATE - INTERVAL 3 MONTH)";
					}
				}
				

				if(self::$from != '' && self::$to == '')
				{
					$sql .= " AND O.billing_date LIKE '%".self::$from."%'";
				}
				
				if(self::$from != '' && self::$to != '')
				{
					$sql .= " AND O.billing_date >= '".self::$from."' AND O.billing_date <= '".self::$to."'";
				}				

				$sql .= " ORDER BY O.billing_date DESC";

				$res = DB::select($sql);
				if(count($res) == 0)
				{
					return self::$default;
					exit;
				}

				$i = 0;
				foreach($res as $row)
				{
						$data['id_product'] 	= $product;
						$data['date'] 			= $row->billing_date;
						$data['dateFull']		= $row->dateFull;
						$data['description'] 	= ['id' => 1, 'value' => 'vente sur techtablet.fr'];
						$data['qty_change'] 	= $row->quantity;
						$data['qty_real']		= '';
						$data['event'] 			= $e;
						$data['id_order']		= $row->id_reseller_order;

					$fdata[$i] = $data;
					$i++;
				}

				return $fdata;

			break;

			case 4:

				$sql = 'SELECT *, DATE(date_updated) AS du, date_updated AS dateFull FROM '.self::$event[$e]['table'].' 
						WHERE id_product = '.$product;

				if(self::$from != '' && self::$to == '')
				{
					$sql .= ' AND date_updated LIKE "%'.self::$from.'%"';
				}
				
				if(self::$from != '' && self::$to != '')
				{
					$sql .= ' AND DATE(date_updated) >= "'.self::$from.'" AND DATE(date_updated) <= "'.self::$to.'"';
				}

				$sql .= ' ORDER BY id DESC';

				$res = DB::select($sql);
				if(count($res) == 0)
				{
					return self::$default;
					exit;
				}

				$i = 0;
				foreach($res as $row)
				{
						$data['id_product'] 	= $row->id_product;
						$data['date'] 			= $row->du;
						$data['dateFull']		= $row->dateFull;
						$data['description'] 	= json_decode($row->reason);
						$data['qty_change'] 	= $row->qty_change;
						$data['qty_real']		= $row->qty_real;
						$data['event'] 			= $e;
						$data['id_order']		= 0;

					$fdata[$i] = $data;
					$i++;
				}

				return $fdata;

			break;

			default:
				return self::$default;
		}
	}
}
?>