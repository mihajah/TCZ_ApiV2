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

	public static function getAllProductsWithFilter(&$total = 0, $range = null, $filter = null, $search = null, $quicksort = null)
	{
		$dbmatch = 	[
						"type" 			=> "T.id_type",
						"fordevice" 	=> "F.id_device",
						"feature" 		=> "FE.id_feature",
						"supplier" 		=> "SU.id_supplier",
						"tag" 			=> "TA.id_tag",
						"subtype" 		=> "S.id_subtype",
						"color" 		=> "C.id_color",
						"material" 		=> "M.id_material",
						"pattern" 		=> "A.id_pattern",
						"active" 		=> "P.active",
						"obsolete" 		=> "Q.is_obsolete",
						"check" 		=> "Q.is_check",
						"fordevicewith" => "DW.id_fordevicewith",
						"brand" 		=> "BR.id_brand",
						"spicture1"		=> "Q.spicture1",
						"numbox" 		=> "Q.numbox"
					];

		if(isset($filter['id']) && !empty($filter['id'])) 
		{
			$request 	= "SELECT DISTINCT id_product FROM ps_product WHERE id_product = ".$filter['id'];
			$start 		= 0;
			$end 		= 0;
		} 
		else 
		{
			// define the extra filter array and the attribute filter array
			$dbfilters = []; 
			// build the filter arrays

			if(isset($filter)) 
			{
				foreach($filter as $key => $value)
				{
					if ($key == "quantitylowerthan" && intval($value) > 0) 
					{
						$fstring = "SA.quantity < ".$value;
					} 
					else 
					{
						//print $key." '".$value."'<br>";
						if(trim($value) == "")
							continue;
						// Explode the lists into single parameters
						$flist = split(";", $value);
						if(!count($flist)) 
						{
							//print " is empty ";
							continue;
						}

						if(!isset($dbmatch[$key])) 
						{
							//print " is empty2 ";
							continue;
						}

						// Now build the filters array
						$fstring = "(".$dbmatch[$key]." = '".trim($flist[0])."' ";
						for($i = 1; $i < count($flist); ++$i) 
						{
							$fstring = $fstring." OR ".$dbmatch[$key]." = '".trim($flist[$i])."' ";
						}
					
						$fstring = $fstring.")";
					}

					$dbfilters[] = $fstring;
				}
			}

			//print_r($dbfilters);
			$request = " SELECT DISTINCT P.id_product,N.name,Q.sold30,Q.sold60,Q.sold,P.ean13 FROM ps_product as P
						 LEFT JOIN ps_product_lang AS N ON P.id_product = N.id_product AND N.id_lang =1
						 LEFT JOIN apb_prd AS Q ON P.id_product = Q.id_product
						 LEFT JOIN apb_types AS T ON Q.id_type = T.id_type
						 LEFT JOIN apb_subtypes AS S ON Q.id_subtype = S.id_subtype
						 LEFT JOIN apb_colors AS C ON Q.id_color = C.id_color
						 LEFT JOIN apb_materials AS M ON Q.id_material = M.id_material
						 LEFT JOIN apb_prd_supplier AS SU ON Q.id_product = SU.id_product
						 LEFT JOIN apb_patterns AS A ON Q.id_pattern = A.id_pattern
						 LEFT JOIN apb_prd_fordevice as F ON P.id_product = F.id_product
						 LEFT JOIN apb_prd_feature as FE ON P.id_product = FE.id_product
						 LEFT JOIN apb_prd_fordevicewith as DW ON P.id_product = DW.id_product
						 LEFT JOIN apb_prd_tag as TA ON P.id_product = TA.id_product
						 LEFT JOIN apb_brands as BR ON Q.id_brand = BR.id_brand
						 LEFT JOIN ps_stock_available as SA ON P.id_product = SA.id_product
					   ";

			if(isset($filter['newproduct']) && $filter['newproduct']) 
			{
				$request .= " LEFT JOIN apb_prd_order as ORD ON P.id_product = ORD.id_product ";
			}

			if(count($dbfilters) || !empty($search)) 
			{
				$request .= " WHERE ";
			}

			for($i = 0; $i<count($dbfilters); $i++) 
			{
				if($i)
					$request .= " AND ".$dbfilters[$i];
				else
					$request .= $dbfilters[$i];
					
			}

			if(!empty($search)) 
			{
				if (count($dbfilters)) 
				{
					$request .= " AND ";
				}

				$request .= "N.name LIKE '%".$search."%'";
			}

			//print "---->".$filter['newproduct']."<----";
			if(isset($filter['newproduct']) && $filter['newproduct']) 
			{
				$request .= " AND (ORD.id_order IS NULL OR ORD.qty_received = 0) AND P.id_product>1500";
				//print $request;
			}


			//$request.=" AND Q.id_brand = 0 ";
			
			// Add limits if any available
			if(!empty($range)) 
			{
				$start 	= $range[0];
				$end 	= $range[1];
			}

			if(is_null($quicksort)) 
			{
				$request .= " ORDER BY P.id_product DESC";
			} 
			else if($quicksort == "sold") 
			{
				$request .= " ORDER BY Q.sold DESC";
			}
			else if($quicksort == "sold30") 
			{
				$request .= " ORDER BY Q.sold30 DESC";
			} 
			else if($quicksort == "sold60") 
			{
				$request .= " ORDER BY Q.sold60 DESC";
			} 
			else if($quicksort == "wishbuy") 
			{
				$request .= " ORDER BY Q.wishbuy DESC";
			}
		}

		//$request .= " LIMIT ".$start.",".$end;
	
		//print "<br>".$request."<br>";
		
		$res = DB::select($request);
		
		if(count($res) == 0) 
		{
			return [];
		}
		
		// only one id kept
		$n 				= 0;
		$products 		= [];
		$products[$n] 	= $res[0]->id_product;
		++$n;

		for($i = 1; $i < count($res); ++$i) 
		{
			//if ($res[$i]['name']
			if ($products[$n-1] != $res[$i]->id_product) 
			{
				$products[$n] = $res[$i]->id_product;
				++$n;
			}
		}

		$total = $n;
		
		if (!empty($range))
			$finalarray = array_slice($products, $start, $end - $start + 1);
		else
			$finalarray = $products;

		return $finalarray;
	}
}
?>