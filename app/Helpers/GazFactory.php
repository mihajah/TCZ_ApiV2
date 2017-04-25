<?php
namespace App\Helpers;
use DB;

class GazFactory
{

	public static function getProductSellingDataForYear($id, $thisYear, $site)
	{

		if($site=="techtablet"){
			$result = array();
		
		
		$mysql = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-01-01' AND O.billing_date<'".$thisYear."-01-31'";
		$res = DB::select($mysql);
		$res = collect($res)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res[0]['totalNumber']!=null){
			$result['Janvier']=$res[0]['totalNumber'];
		}
		else{
			$result['Janvier']=0;
		}
		
		$mysql2 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-02-01' AND O.billing_date<'".$thisYear."-02-29'";
		$res2 = DB::select($mysql2);	
		$res2 = collect($res2)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res2[0]['totalNumber']!=null){
			$result['Fevrier']=$res2[0]['totalNumber'];
		}
		else{
			$result['Fevrier']=0;
		}
		
		$mysql3 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
					LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
					WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-03-01' AND O.billing_date<'".$thisYear."-03-31'";
		$res3 = DB::select($mysql3);	
		$res3 = collect($res3)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res3[0]['totalNumber']!=null){
			$result['Mars']=$res3[0]['totalNumber'];
		}
		else{
			$result['Mars']=0;
		}
		
		$mysql4 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-04-30' AND O.billing_date<'".$thisYear."-04-30'";
		$res4 = DB::select($mysql4);	
		$res4 = collect($res4)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res4[0]['totalNumber']!=null){
			$result['Avril']=$res4[0]['totalNumber'];
		}
		else{
			$result['Avril']=0;
		}
		
		$mysql5 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-05-01' AND O.billing_date<'".$thisYear."-05-31'";
		$res5 = DB::select($mysql5);	
		$res5 = collect($res5)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res5[0]['totalNumber']!=null){
			$result['Mai']=$res5[0]['totalNumber'];
		}
		else{
			$result['Mai']=0;
		}
		
		$mysql6 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-06-01' AND O.billing_date<'".$thisYear."-06-30'";
		$res6 = DB::select($mysql6);	
		$res6 = collect($res6)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res6[0]['totalNumber']!=null){
			$result['Juin']=$res6[0]['totalNumber'];
		}
		else{
			$result['Juin']=0;
		}
		
		$mysql7 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-07-01' AND O.billing_date<'".$thisYear."-07-31'";
		$res7 = DB::select($mysql7);	
		$res7 = collect($res7)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res7[0]['totalNumber']!=null){
			$result['Juillet']=$res7[0]['totalNumber'];
		}
		else{
			$result['Juillet']=0;
		}
		
		$mysql8 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-08-01' AND O.billing_date<'".$thisYear."-08-31'";
		$res8 = DB::select($mysql8);	
		$res8 = collect($res8)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res8[0]['totalNumber']!=null){
			$result['Aout']=$res8[0]['totalNumber'];
		}
		else{
			$result['Aout']=0;
		}
		
		$mysql9 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-09-01' AND O.billing_date<'".$thisYear."-09-30'";
		$res9 = DB::select($mysql9);	
		$res9 = collect($res9)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res9[0]['totalNumber']!=null){
			$result['Septembre']=$res9[0]['totalNumber'];
		}
		else{
			$result['Septembre']=0;
		}
		
		$mysql10 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-10-01' AND O.billing_date<'".$thisYear."-10-31'";
		$res10 = DB::select($mysql10);	
		$res10 = collect($res10)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res10[0]['totalNumber']!=null){
			$result['Octobre']=$res10[0]['totalNumber'];
		}
		else{
			$result['Octobre']=0;
		}
		
		$mysql11 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-11-01' AND O.billing_date<'".$thisYear."-11-30'";
		$res11 = DB::select($mysql11);	
		$res11 = collect($res11)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res11[0]['totalNumber']!=null){
			$result['Novembre']=$res11[0]['totalNumber'];
		}
		else{
			$result['Novembre']=0;
		}
		
		$mysql12 = "SELECT SUM(C.quantity) AS totalNumber FROM `apb_reseller_carts` AS C
						LEFT JOIN `apb_reseller_orders` AS O ON O.id_reseller_order = C.id_reseller_order
						WHERE C.id_product='".$id."' AND O.status>3 AND O.billing_date>'".$thisYear."-12-01' AND O.billing_date<'".$thisYear."-01-31'";
		$res12 = DB::select($mysql12);	
		$res12 = collect($res12)->map(function($x){
			return (array) $x;
		})->toArray();

		if($res12[0]['totalNumber']!=null){
			$result['Decembre']=$res12[0]['totalNumber'];
		}
		else{
			$result['Decembre']=0;
		}
			
			return $result;
				
		}

		if($site=="touchiz"){
				$result = array();

			$mysql = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-01-01' AND O.date_add<'".$thisYear."-01-31'";
				$res = DB::select($mysql);	
				$res = collect($res)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res[0]['totalNumber']!=null){
			$result['Janvier']=$res[0]['totalNumber'];
			}
			else{
				$result['Janvier']=0;
			}
				
			$mysql2 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-02-01' AND O.date_add<'".$thisYear."-02-29'";
			$res2 = DB::select($mysql2);	
			$res2 = collect($res2)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res2[0]['totalNumber']!=null){
			$result['Fevrier']=$res2[0]['totalNumber'];
			}
			else{
				$result['Fevrier']=0;
			}
			
			$mysql3 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-03-01' AND O.date_add<'".$thisYear."-03-31'";
			$res3 = DB::select($mysql3);	
			$res3 = collect($res3)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res3[0]['totalNumber']!=null){
			$result['Mars']=$res3[0]['totalNumber'];
			}
			else{
				$result['Mars']=0;
			}
			
			
			$mysql4 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-04-01' AND O.date_add<'".$thisYear."-04-30'";
			$res4 = DB::select($mysql4);	
			$res4 = collect($res4)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res4[0]['totalNumber']!=null){
			$result['Avril']=$res4[0]['totalNumber'];
			}
			else{
				$result['Avril']=0;
			}
			
			
			$mysql5 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-05-01' AND O.date_add<'".$thisYear."-05-31'";
			$res5 = DB::select($mysql5);	
			$res5 = collect($res5)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res5[0]['totalNumber']!=null){
			$result['Mai']=$res5[0]['totalNumber'];
			}
			else{
				$result['Mai']=0;
			}
			
			
			$mysql6 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-06-30' AND O.date_add<'".$thisYear."-06-30'";
			$res6 = DB::select($mysql6);	
			$res6 = collect($res6)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res6[0]['totalNumber']!=null){
				$result['Juin']=$res6[0]['totalNumber'];
			}
			else{
				$result['Juin']=0;
			}
			
			
			$mysql7 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-07-01' AND O.date_add<'".$thisYear."-07-31'";
			$res7 = DB::select($mysql7);	
			$res7 = collect($res7)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res7[0]['totalNumber']!=null){
				$result['Juillet']=$res7[0]['totalNumber'];
			}
			else{
				$result['Juillet']=0;
			}
			
			
			$mysql8 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-08-01' AND O.date_add<'".$thisYear."-08-31'";
			$res8 = DB::select($mysql8);	
			$res8 = collect($res8)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res8[0]['totalNumber']!=null){
				$result['Aout']=$res8[0]['totalNumber'];
			}
			else{
				$result['Aout']=0;
			}
			
			
			$mysql9 = "	SELECT SUM( C.quantity ) AS totalNumber
							FROM ps_cart_product AS C
							LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-09-01' AND O.date_add<'".$thisYear."-09-30'";
			$res9 = DB::select($mysql9);	
			$res9 = collect($res9)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res9[0]['totalNumber']!=null){
				$result['Septembre']=$res9[0]['totalNumber'];
			}
			else{
				$result['Septembre']=0;
			}
			
			
			$mysql10 =" SELECT SUM( C.quantity ) AS totalNumber
					FROM ps_cart_product AS C
					LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
					WHERE C.id_product = '".$id."'
					AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-10-01' AND O.date_add<'".$thisYear."-10-31'";
			$res10 = DB::select($mysql10);	
			$res10 = collect($res10)->map(function($x){
							return (array) $x;
						})->toArray();

				if($res10[0]['totalNumber']!=null){
					$result['Octobre']=$res10[0]['totalNumber'];
				}
				else{
					$result['Octobre']=0;
				}
			
			
			$mysql11 = "	SELECT SUM( C.quantity ) AS totalNumber
					FROM ps_cart_product AS C
					LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
					WHERE C.id_product = '".$id."'
					AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-11-01' AND O.date_add<'".$thisYear."-11-30'";
			$res11 = DB::select($mysql11);	
			$res11 = collect($res11)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res11[0]['totalNumber']!=null){
				$result['Novembre']=$res11[0]['totalNumber'];
			}
			else{
				$result['Novembre']=0;
			}
			
			
			$mysql12 = "	SELECT SUM( C.quantity ) AS totalNumber
					FROM ps_cart_product AS C
					LEFT JOIN ps_orders AS O ON O.id_cart = C.id_cart
					WHERE C.id_product = '".$id."'
					AND ( O.current_state=4 OR O.current_state=5 ) AND O.date_add>'".$thisYear."-12-01' AND O.date_add<'".$thisYear."-12-31'";
			$res12 = DB::select($mysql12);	
			$res12 = collect($res12)->map(function($x){
							return (array) $x;
						})->toArray();

			if($res12[0]['totalNumber']!=null){
				$result['Decembre']=$res12[0]['totalNumber'];
			}
			else{
				$result['Decembre']=0;
			}
				
			return $result;
		}
		
	}

	public static function mailShippingHtmlBody($nom, $numcommande, $lien = "", $numsuivis = "")
	{
		$str2="<div id='container' style='width:610px;margin:auto;text-align:center;'>";
		$str2.="<div style='border-bottom:1px solid #cccccc;padding-bottom:10px;'><img src='http://www.techtablet.fr/ordermodule/LOGOfactures.png' />";
		$str2.="</div>";
		$str2.="<div style='padding-top:10px;color:#555454;font-family: Arial;text-align:left;font-size: 17px;'>";
		$str2.="		<div style='padding-bottom: 20px;margin-top: 10px;'>";
		$str2.="		<span style='font-size:20px;'> Bonjour ".$nom.", </span><br/>";
		$str2.="		VOTRE COMMANDE A ÉTÉ EXPÉDIÉE";
		$str2.="		</div>";
		$str2.="		<div style='border-top:1px solid #cccccc;padding-top: 10px;'>";
		$str2.="			Votre commande ayant la référence <span style='font-weight: 800;color:#333333;'>".$numcommande." </span> vient d'être expédiée. <br/>";
		$str2.="		<p>";
		if ($numsuivis != "") {
			$str2.="				Votre numéro de suivi est : <span style='font-weight: 800;'>".$numsuivis."</span> <br/>";
			$str2.="		Pour suivre l'avancement de votre livraison, vous pouvez saisir ce numéro à l'adresse suivante :  <br/>";
			$str2.="				<a href='http://www.csuivi.courrier.laposte.fr/suivi/index?id=".$numsuivis."'>http://www.csuivi.courrier.laposte.fr/suivi/index?id='".$numsuivis."'</a> <br/>";	
		}
		if ($lien != "") {
			$str2.="			Votre facture est disponible en téléchargement <a href='".$lien."'> sur la page de votre commande</a>";
		} 
		$str2.="		</p>";
		$str2.="		<p>Merci d'avoir effectué vos achats chez TechTablet</p>";
		$str2.="		</div>";
		$str2.="</div>";
		$str2.="</div>";
		
		return $str2;
	}
}
?>