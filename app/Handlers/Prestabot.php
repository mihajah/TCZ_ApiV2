<?php
namespace App\Helpers;
use DB;
use App\Device;
use App\Product;
use App\Collection;
use App\Stock;
use Config;
use Mail;

class Prestabot
{
	protected static $table_fordevice 		= 'apb_prd_fordevice';
	protected static $table_brand 			= 'apb_devices_values';
	protected static $table_type 			= 'apb_types';
	protected static $table_subtype 		= 'apb_subtypes'; 
	protected static $table_colors 			= 'apb_colors'; 
	protected static $table_brands 			= 'apb_brands';
	protected static $table_pfeature 		= 'apb_prd_feature';   
	protected static $table_feature 		= 'apb_features';   
	protected static $table_buyingprice		= 'apb_prd_order';         
	protected static $table_tag 			= ['PT' => 'apb_prd_tag', 'T' => 'apb_tags'];   
	protected static $table_fordevicewith 	= ['F' => 'apb_prd_fordevicewith', 'D' => 'apb_fordevicewith'];   
	protected static $table_infomail 		= 'apb_infomail';   


	/**
	* Product
	*/
	public static function product($id, $prop)
	{
		//get data
		if($prop == 'fordevice')
		{
			$forDevice =  [];
			$sql = 'SELECT id_device FROM '.self::$table_fordevice.' 
				WHERE id_product = :id_product';
			$results = DB::select($sql, ['id_product' => $id]);
			if(count($results) == 0)
				return ['id' => '', 'value' => ''];

			foreach($results as $dv)
			{
				$id_device   = $dv->id_device;
				$name_device = Device::find($id_device)->name;
				$forDevice[] = ['id' => $id_device, 'name' => $name_device];
			}
			
			return $forDevice;
		}

		if($prop == 'forbrand')
		{
			$forBrand = [];
			$device   = $id;
			foreach($device as $dv)
			{
				if(Device::find($dv['id']))
				{
					$brand_id = Device::find($dv['id'])->brand;
					$sql = 'SELECT value FROM '.self::$table_brand.' 
						WHERE id_value = :value';
					$results = DB::select($sql, ['value' => $brand_id]);
					if(count($results) > 0)
					{
						$forBrand[] = ['id' => $brand_id, 'name' => $results[0]->value];
					}
				}						
			}

			if(count($forBrand) == 0)
			{
				return ['id' => '', 'name' => ''];
			}
			
			return $forBrand;
		}

		if($prop == 'type')
		{
			$sql = 'SELECT id_type, type_name FROM '.self::$table_type.' 
					WHERE id_type = :type';
			$results = DB::select($sql, ['type' => $id]);
			if(count($results) > 0)
			{
				return ['id' => $id, 'name' => $results[0]->type_name];
			}
			else
			{
				return ['id' => '', 'name' => ''];
			}
			
		}

		if($prop == 'subtype')
		{
			$sql = 'SELECT id_subtype, subtype_name FROM '.self::$table_subtype.' 
					WHERE id_subtype = :subtype';
			$results = DB::select($sql, ['subtype' => $id]);
			if(count($results) > 0)
			{
				return ['id' => $id, 'name' => $results[0]->subtype_name];
			}
			else
			{
				return ['id' => '', 'name' => ''];
			}
			
		}

		if($prop == 'price_reseller')
		{
			$product = Product::find($id);
			if($product->price_reseller > 0)
			{
				return ['value' => $product->price_reseller];
			}

			if(!Collection::find($product->id_collection))
			{
				return ['value' => ''];
			}

			$price = Collection::find($product->id_collection)->price;
			return ['value' => $price];

		}

		if($prop == 'cover')
		{
			$type = $id['type'];
			$id   = $id['id'];
			

			if(empty($id))
			{
				return null;
			}
				
			$digits = str_split($id);

			if($type == 'cover')
			{
				$path = "pic/source/";
			}
			else if($type == 'thumb')
			{
				$path = "pic/thumbnails/";
			}			
			
			foreach($digits as $d) 
			{
				$path.= $d."/";
			}

			return Config::get('constants.PROD_BASE_URL').$path."1.jpg";
		}

		if($prop == 'color')
		{
			$id_color = Product::find($id)->id_color;
			$sql = 'SELECT id_color, color_name, name FROM '.self::$table_colors.' 
					WHERE id_color = :id';
			$results = DB::select($sql, ['id' => $id_color]);
			if(count($results) > 0)
			{
				return ['id' => $id_color, 'name' => $results[0]->color_name, 'alt_name' => $results[0]->name];
			}
			else
			{
				return ['id' => '', 'name' => '', 'alt_name' => ''];
			}
			
		}

		if($prop == 'tag')
		{
			return self::getTag($id);		
		}

		if($prop == 'fordevicewith')
		{
			return self::getForDeviceWith($id);
		}

		if($prop == 'feature')
		{
			return self::getFeature($id);
		}

		if($prop == 'buyingprice')
		{
			return self::getBuyingPrice($id);
		}

		if($prop == 'orderedqtty')
		{
			return self::getOrderedQty($id);
		}

		if($prop == 'manufacturer')
		{
			$idm = Product::find($id)->id_manufacturer;
			$sql = "SELECT * 
					FROM  `ps_manufacturer` 
					WHERE id_manufacturer = :idm";
			$results = DB::select($sql, ['idm' => $idm]);
			
			if(count($results) > 0)
			{
				return ['id' => $res[0]->id_manufacturer, 'name' 	=> $res[0]->name];
			}
			else
			{
				return ['id' => '', 'name' => ''];
			}
		}		

		//set data
		if($prop == 'set_oldd')
		{
			$sql = "UPDATE  apb_prd SET old_description = ? WHERE id_product = ?";
			DB::update($sql, [1, $id]);
		}

		if($prop == 'set_tag')
		{
			$value = $id['value'];
			$id    = $id['id'];

			$sql = "DELETE FROM apb_prd_tag WHERE id_product = ?";
			DB::delete($sql, [$id]);
			
			if (trim($value) == "")
				return;
			
			$args = explode(";", $value);
			foreach($args as $arg) 
			{
				$sql = "INSERT INTO apb_prd_tag VALUES (?, ?)";
				DB::insert($sql, [$id, trim($arg)]);
			}

			self::product($id, 'set_oldd');
		}

		if($prop == 'set_fordevicewith')
		{
			$value = $id['value'];
			$id    = $id['id'];

			$sql = "DELETE FROM apb_prd_fordevicewith WHERE id_product = ?";
			DB::delete($sql, [$id]);
			
			if (trim($value) == "")
				return;
			
			$args = explode(";", $value);
			foreach($args as $arg) 
			{
				$sql = "INSERT INTO apb_prd_fordevicewith VALUES (?, ?)";
				DB::insert($sql, [$id, trim($arg)]);
			}

			self::product($id, 'set_oldd');
		}

		if($prop == 'set_quantity')
		{
			$value = $id['value'];
			$id    = $id['id'];

			Stock::set($id, $value);
		}

		if($prop == 'set_supplier')
		{
			$value = $id['value'];
			$id    = $id['id'];

			$sql = "DELETE FROM apb_prd_supplier WHERE id_product = ?";
			DB::delete($sql, [$id]);
			
			if (trim($value) == "")
				return;
			
			$args = explode(";", $value);
			$first = 1;
			foreach($args as $arg) 
			{
				$sql = "INSERT INTO apb_prd_supplier VALUES (?, ?, ?)";
				DB::insert($sql, [$id, trim($arg), $first]);
				$first = 0;
			}
		}

		if($prop == 'set_fordevice')
		{
			$value = $id['value'];
			$id    = $id['id'];
			

			$sql = "DELETE FROM apb_prd_fordevice WHERE id_product = ?";
			DB::delete($sql, [$id]);
			
			if (trim($value) == "")
				return;
			
			$args = explode(";", $value);
			foreach($args as $arg) 
			{
				$sql = "INSERT INTO apb_prd_fordevice VALUES (?, ?)";
				DB::insert($sql, [$id, trim($arg)]);
			}

			self::product($id, 'set_oldd');
		}

		if($prop == 'set_feature')
		{
			$value = $id['value'];
			$id    = $id['id'];

			$sql = "DELETE FROM apb_prd_feature WHERE id_product = ?";
			DB::delete($sql, [$id]);
			
			if (trim($value) == "")
				return;
			
			$args = explode(";", $value);
			foreach($args as $arg) 
			{
				$sql = "INSERT INTO apb_prd_feature VALUES (?, ?)";
				DB::insert($sql, [$id, trim($arg)]);
			}

			self::product($id, 'set_oldd');
		}
	}

	/**
	* Brand
	*/
	public static function getAllBrand()
	{
		$brand = [];
		$sql = "SELECT * FROM ".self::$table_brands;
		$results = DB::select($sql);
		if(count($results) > 0)
		{
			foreach($results as $row)
			{
				$brand[] = ['id' => $row->id_brand, 'name' => $row->brand_name];
			}

			return $brand;
		}
		else
		{
			return $brand;
		}
	}

	/**
	* Order
	*/
	/*public static function getResellerOrdersID($staging = FALSE)
	{
		$order = [];
		$sql = "SELECT O.id_reseller_order FROM apb_reseller_orders".($staging?"_staging":"")." AS O ORDER BY O.id_reseller_order DESC";
		$result = DB::select($sql);
		if(count($result) > 0)
		{

			return
		}

		return $order;
	} */

	/**
	* Other
	*/

	public static function recordInfoMail($raw)
	{
		$error = FALSE;
		$sql = 'INSERT INTO '.self::$table_infomail.' (mail, id_product, date_added) VALUES (?, ?, ?)';
		$db = DB::insert($sql, [$raw['mail'], $raw['id_product'], @date('Y-m-d')]);

		if(!$db)
		{
			$error = TRUE;
		}

		$sent = Mail::send(['html' => 'emails.ps_notif.fiche_produit'], ['url' => $raw['url']], function($msg) use ($raw) {
			$msg->from(Config::get('constants.TC_SHOP_EMAIL'), Config::get('constants.TC_SHOP_NAME'));
			$msg->to($raw['mail'])->subject('Votre fiche produit vous attend sur Touchiz');
		});

		if(!$sent)
		{
			$error = TRUE;
		}

		return $error;
	}

	/**
	* Internal method
	*/

	protected static function getOrderedQty($id)
	{
		$qty = 0;
		$sql = "SELECT S.* FROM apb_prd_order AS S";
		$results = DB::select($sql);
		
		for ($i=0; $i<count($results); $i++) 
		{
				if($results[$i]->id_product == $id)
				{
					$sql2 = "SELECT * FROM apb_orders WHERE id_order = :id_order";
					$results2 = DB::select($sql2, ['id_order' => $results[$i]->id_order]);
					if($results2[0]->step > 0 && $results2[0]->step < 3 && $results2[0]->processed == 0 )
					{
						$qty = $qty + $results[$i]->qty_wanted;
						
					}					
				}
				
		}

		return $qty;
	}

	protected static function getBuyingPrice($id)
	{
		$sql = "SELECT buying_price 
				FROM ".self::$table_buyingprice." 
				WHERE id_product = :id ORDER BY id_order DESC";
		$results = DB::select($sql, ['id' => $id]);

		if (count($results)) 
		{
			return $results[0]->buying_price;
		} 
		else 
		{
			return 0.0;
		}
	}

	protected static function getFeature($id)
	{
		$sql = "SELECT D.feature_name,D.id_feature
				FROM ".self::$table_pfeature." AS F
				LEFT JOIN ".self::$table_feature." AS D ON F.id_feature = D.id_feature
				WHERE F.id_product = :id";
		$results = DB::select($sql, ['id' => $id]);

		if(count($results) == 0) 
		{
			return ['display' => '', 'value' => ''];
		}
		
		$str  = '';
		$str2 = '';

		for ($i=0; $i<count($results); $i++) 
		{
			if ($i == (count($results)-1)) 
			{
				$str  .= $results[$i]->feature_name;
				$str2 .= $results[$i]->id_feature;
			} 
			else 
			{
				$str  .= $results[$i]->feature_name.";";
				$str2 .= $results[$i]->id_feature.";";
			}
		}

		$final 				= [];
		$final['display'] 	= $str;
		$final['value'] 	= $str2;


		return $final;
	}

	protected static function getTag($id) 
	{
		$sql = "SELECT T.tag_name,T.id_tag
				FROM ".self::$table_tag['PT']." AS PT
				INNER JOIN ".self::$table_tag['T']." AS T ON PT.id_tag = T.id_tag
				WHERE PT.id_product = :id 
				ORDER BY T.popular DESC";
		$results = DB::select($sql, ['id' => $id]);

		if(count($results)) 
		{
			return ['display' => '', 'value' => ''];
		}
		
		$str  = '';
		$str2 = '';

		for($i=0; $i<count($results); $i++) 
		{
			if($i == (count($results) - 1)) 
			{
				$str  .= $results[$i]->tag_name;
				$str2 .= $results[$i]->id_tag;
			} 
			else 
			{
				$str  .= $results[$i]->tag_name.';';
				$str2 .= $results[$i]->id_tag.';';
			}
		}

		$final 				= [];
		$final['display'] 	= $str;
		$final['value'] 	= $str2;

		return $final;
	}

	protected static function getForDeviceWith($id) 
	{
		$sql = "SELECT D.fordevicewith_name,D.id_fordevicewith
				FROM ".self::$table_fordevicewith['F']." AS F
				INNER JOIN ".self::$table_fordevicewith['D']." AS D ON F.id_fordevicewith = D.id_fordevicewith
				WHERE F.id_product = :id";
		$results = DB::select($sql, ['id' => $id]);

		if(empty($results)) 
		{
			return ['display' => '', 'value' => ''];
		}
		
		$str = '';
		$str2 = '';

		for($i=0; $i<count($results); $i++) 
		{
			if($i == (count($results) - 1)) 
			{
				$str  .= $results[$i]->fordevicewith_name;
				$str2 .= $results[$i]->id_fordevicewith;
			} 
			else 
			{
				$str  .= $results[$i]->fordevicewith_name.';';
				$str2 .= $results[$i]->id_fordevicewith.';';
			}
		}

		$final 				= [];
		$final['display'] 	= $str;
		$final['value'] 	= $str2;

		return $final;
	}

}
?>