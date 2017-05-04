<?php
namespace App\Helpers;
use DB;

class Stat
{
	protected static $year_base = '2015'; //pour les ventes normalisés

	public static function getStatForDevice($device)
	{
		$graph = '';

		if(isset($_GET['g']))
		{
			$graph = $_GET['g'];
		}

			$stat 			= [];
			$year_sales 	= self::getAllSales();
			$year_possible 	= self::getxAxis($device);

			foreach($year_possible as $yp)
			{
					for($i=1; $i<13; $i++)
					{
						//$date = date('Y', strtotime('-1 year')).'-';
						$date = $yp.'-';

						if($i < 10)
						{
							$mm = '0'.$i;
						}
						else
						{
							$mm = $i;
						}

						$date .= $mm;

						$add = self::watchStarDate($device, $date);

						if($add)
						{
							if($graph == 'g2')
							{
								$month_sales = self::getSalesByMonth($device, $date);				
								$modifier 	 = self::generateModifier($year_sales, $mm);
								$sales 		 = $month_sales->TQ * $modifier;
								$sales       = ['TQ' => $sales];
							}
							else
							{
								$sales 		 = self::getSalesByMonth($device, $date);
							}

							$stat[$date] = ['sales' => $sales, 'period' => $date];
						}

						
					}
			}
			

			$final = [$device => $stat];
			return $final;		
	}

	/**
 	* @Retourne le nombre de ventes pour un mois pour un produit
 	*/
	protected static function getSalesByMonth($device, $date)
	{
		$qty = 0;

		//vente touchiz
		$sql = "SELECT CP.quantity AS TQ
			FROM  `ps_cart_product` AS CP
			LEFT JOIN  `ps_orders` AS O ON O.id_cart = CP.id_cart
			INNER JOIN apb_prd AS PRD ON PRD.id_product = CP.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = CP.id_product
			WHERE PRD.id_collection = 1
			AND FD.id_device = $device
			AND O.id_order > 0
			AND O.date_upd LIKE '%$date%'
			ORDER BY O.date_upd DESC";		
			
		$list = DB::select($sql);
		if(count($list) > 0)
		{
			foreach($list as $tq)
			{
				$qty += $tq->TQ;
			}
		}

		//vente techtablet
		$sql = "SELECT C.quantity as TQ
			FROM  `apb_reseller_carts` AS C
			LEFT JOIN  `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
			INNER JOIN apb_prd AS PRD ON PRD.id_product = C.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = C.id_product
			WHERE O.status >1
			AND PRD.id_collection =1
			AND C.id_reseller_order >0
			AND FD.id_device = $device
			AND billing_date !=  '0000-00-00 00:00:00'
			AND billing_date LIKE  '%$date%'
			GROUP BY C.id_reseller_order
			ORDER BY O.billing_date DESC
			"; 

		$list = DB::select($sql);
		if(count($list) > 0)
		{
			foreach($list as $tq)
			{
				$qty += $tq->TQ;
			}
		}

		$final = ['TQ' => $qty];
		return $final;
	}

	/**
 	* @Retourne le nombre total de ventes pour l'annéee de base
 	* pour toutes les marques
 	* @parametre mois (filtre resultat)
 	*/
	protected static function getAllSales($month = '')
	{
		$date = self::$year_base;
		if($month)
		{
			$date .= '-'.$month; 
		}

		$nva = 0;

		//vente touchiz
		$sql = "SELECT CP.quantity AS TQ
			FROM  `ps_cart_product` AS CP
			LEFT JOIN  `ps_orders` AS O ON O.id_cart = CP.id_cart
			INNER JOIN apb_prd AS PRD ON PRD.id_product = CP.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = CP.id_product
			WHERE PRD.id_collection = 1
			AND O.id_order > 0
			AND O.date_upd LIKE '%$date%'
			ORDER BY O.date_upd DESC";

		$list = DB::select($sql);
		if(count($list) > 0)
		{
			foreach($list as $tq)
			{
				$nva += $tq->TQ;
			}
		}

		//vente techtablet
		$sql = "SELECT C.quantity as TQ
			FROM  `apb_reseller_carts` AS C
			LEFT JOIN  `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
			INNER JOIN apb_prd AS PRD ON PRD.id_product = C.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = C.id_product
			WHERE O.status >1
			AND PRD.id_collection =1
			AND C.id_reseller_order > 0
			AND billing_date !=  '0000-00-00 00:00:00'
			AND billing_date LIKE  '%$date%'
			GROUP BY C.id_reseller_order
			ORDER BY O.billing_date DESC
			"; 

		$list = DB::select($sql);
		if(count($list) > 0)
		{
			foreach($list as $tq)
			{
				$nva += $tq->TQ;
			}
		}

		$final = ['NV_a' => $nva];
		return $final;
	}

	/**
	* @Retourne le Modificateur(Mois)
	*/
	protected static function generateModifier($year_sales, $month)
	{
		$month_sales = self::getAllSales($month);
		$modifier    = $year_sales['NV_a'] / (12*$month_sales['NV_a']);
		$modifier 	 = round($modifier, 2);

		return $modifier;
	}

	/**
	* @Recupere les années possibles pour l'axe X
	*/
	protected static function getxAxis($device)
	{
		$year = [];
		
		$sql = "SELECT DATE_FORMAT( date_upd,  '%Y' ) AS YEAR
			FROM  `ps_cart_product` AS CP
			LEFT JOIN  `ps_orders` AS O ON O.id_cart = CP.id_cart
			INNER JOIN apb_prd AS PRD ON PRD.id_product = CP.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = CP.id_product
			WHERE PRD.id_collection = 1
			AND FD.id_device = $device
			AND O.id_order > 0
			GROUP BY YEAR
			ORDER BY O.date_upd ASC";

		$list = DB::select($sql);
		if(count($list) > 0)
		{
			foreach($list as $yr)
			{
				$year[] = $yr->YEAR;
			}
		}

		//

		$sql = "SELECT DATE_FORMAT( billing_date,  '%Y' ) AS YEAR
			FROM  `apb_reseller_carts` AS C
			LEFT JOIN  `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
			INNER JOIN apb_prd AS PRD ON PRD.id_product = C.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = C.id_product
			WHERE O.status > 1
			AND PRD.id_collection = 1
			AND C.id_reseller_order >0
			AND FD.id_device = $device
			AND billing_date !=  '0000-00-00 00:00:00'
			GROUP BY YEAR
			ORDER BY O.billing_date ASC";

		$list = DB::select($sql);
		if(count($list) > 0)
		{
			foreach($list as $yr)
			{
				$year[] = $yr->YEAR;
			}
		}

		return $year;
	}

	protected static function getEndDate($device)
	{
		$dtc = 0;
		$dtt = 0;

		$sql = "SELECT DATE_FORMAT( O.date_upd,  '%Y-%m' ) AS datefin
			FROM  `ps_cart_product` AS CP
			LEFT JOIN  `ps_orders` AS O ON O.id_cart = CP.id_cart
			INNER JOIN apb_prd AS PRD ON PRD.id_product = CP.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = CP.id_product
			WHERE PRD.id_collection =1
			AND FD.id_device = $device
			AND O.id_order >0
			ORDER BY O.date_upd DESC 
			LIMIT 0, 1";

		$row = DB::select($sql);

		if(count($row) > 0)
		{
			$dtc = $row[0]->datefin;
		}

		$sql = "SELECT DATE_FORMAT( billing_date,  '%Y-%m' ) as datefin
			FROM  `apb_reseller_carts` AS C
			LEFT JOIN  `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
			INNER JOIN apb_prd AS PRD ON PRD.id_product = C.id_product
			INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = C.id_product
			WHERE O.status >1
			AND PRD.id_collection =1
			AND C.id_reseller_order >0
			AND FD.id_device = $device
			AND billing_date !=  '0000-00-00 00:00:00'
			GROUP BY C.id_reseller_order
			ORDER BY O.billing_date DESC
			LIMIT 0, 1";

		$row = DB::select($sql);

		if(count($row) > 0)
		{
			$dtt = $row[0]->datefin;
		}

		if($dtc)
		{
			$datefin = strtotime($dtc);
		}

		if($dtt)
		{
			$datefin = strtotime($dtt);
		}

		if($dtc && $dtt)
		{
			$dtc = strtotime($dtc);
			$dtt = strtotime($dtt);
			if($dtc >= $dtt)
			{
				$datefin = $dtc;
			}	
			else
			{
				$datefin = $dtt;
			}
		}

		return $datefin;
	}

	protected static function watchStarDate($device, $in)
	{
		$out = 0;

		$sql = "SELECT DATE_FORMAT(date_updated, '%Y-%m') as du
		FROM  `apb_prd_order` AS CP
		LEFT JOIN  `apb_orders` AS O ON O.id_order = CP.id_order
		INNER JOIN apb_prd AS PRD ON PRD.id_product = CP.id_product
		INNER JOIN apb_prd_fordevice AS FD ON FD.id_product = CP.id_product
		WHERE PRD.id_collection =1
		AND FD.id_device = $device
		AND O.id_order > 0
		AND date_updated !=  '0000-00-00 00:00:00'
		ORDER BY date_updated ASC 
		LIMIT 0 , 1";

		$row = DB::select($sql);

		if(count($row) > 0)
		{
			$out = $row[0]->du;
		}

		if($out)
		{
			$start_date = strtotime($out);
			$d2 		= strtotime($in);
			$end_date 	= self::getEndDate($device);

			if($d2 >= $start_date && $d2 <= $end_date)
			{
				$out = TRUE;
			}
			else
			{
				$out = FALSE;
			}
		}

		return $out;
	}
}
?>