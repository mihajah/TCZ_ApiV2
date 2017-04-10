<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Prestashop as PS;
use App\Helpers\Prestabot as PB;
use App\Helpers\GazFactory as GF;
use App\Traits\ModelGetProperties;
use App\Stock;
use Config;
use App\Device;
use App\Order;
use DB;


class Product extends Model {

	
	//
	protected $table 		 			= 'apb_prd';
	protected $primaryKey 	 			= 'id_product'; 
	protected $product_ps_table  		= 'ps_product';	
	protected $product_type_table  		= 'apb_types';	 
	protected $product_material_table  	= 'apb_materials';
	protected $product_pattern_table  	= 'apb_patterns';	
	protected $table_fordevice 			= 'apb_prd_fordevice'; 
	protected $table_spicture 			= 'apb_picturestatus'; 	
	protected $table_brand				= 'apb_brands'; 		
	protected static $collection_pk 	= 'id_collection';
	protected static $collection_fk 	= 'id_collection';
	protected static $url				= 'index.php?id_product=PRODUCT_ID&controller=product';

	use ModelGetProperties;

	/**
	* WS method
	*/
	public static function wsOne($id_ean, $vmode = 'arr')
	{
		$data  = [];
		$id    = PS::product($id_ean, 'PT.id_product'); //beware!! ean -> id conversion
		$full  = self::remapProductAttributes($id);
		$field = [
					'id',
					'name',
					'fordevice',
					'forbrand',
					'type',
					'subtype',
					'ean',
					'pricettc',
					'price_reseller',
					'pictures',
					'quantity',
					'box',
					'color',
					'collection',
					'weight',
					'url',
					'cover',
					'reseller_description',
					'stock'
				 ];

		foreach($field as $key)
		{
			$data[$key] = $full->$key;
		}

		if($vmode == 'obj')
			return (object) $data;

		return $data;
	}

	public static function wsAll($for = '')
	{
		$result = [];

		if($for == 'custom')
		{

			return self::linkPSProduct(TRUE);
		}

		$all = self::linkPSProduct()->orderBy(self::getProp('table').'.id_product', 'desc')->get();
		
		

		foreach($all as $row)
		{
			$hasQuantity = Stock::getAvailable($row->id_product);
			if($hasQuantity)
			{
				$result[] = $row->id_product;
			}
		}

		return $result;
	}

	public static function wsAllBrand()
	{
		return PB::getAllBrand();
	}

	public static function wsForDevice($device)
	{
		return self::getForDevice($device);
	}	

	public static function wsAmazone($id)
	{
		$data = [];
		$full = self::remapProductAttributes($id);
		$field = [
					'id',
					'id_product',
					'name',
					'description',
					'ean',
					'type',
					'subtype',
					'url',
					'material',
					'color',
					'pattern',
					'manufacturer',
					'condition',
					'fordevice',
					'picture1',
					'picture2',
					'picture3',
					'picture4',
					'picture5',
					'picture6',
					'picture7',
					'picture8',
					'picture9',
					'tag',
					'brand',
					'fordevicewith',
					'commentpicture',
					'price',
					'pricettc',
					'quantity',
					'width',
					'height',
					'weight',
					'depth',
					'ppic1',
					'ppic2',
					'feature'
				];
		
		foreach($field as $key)
		{
			$data[$key] = $full->$key;
		}

		return $data;
	}

	public static function wsPmanager($id, $for)
	{
		if($for == 'device')
		{
			return self::getForDevice($id, 'pmanager');
		}
	}

	/**
	* Public method
	*/

	public static function getFullSchema($id, $display = 'both') //key, value, both
	{
		$full = self::remapProductAttributes($id, 'arr');

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

	public static function getInBox($box)
	{
		$selected	= [];
		$candidate 	= self::prepareBoxCandidate();
		$pass 		= $candidate['pass'];
		$obsolete 	= $candidate['obs'];

		if(count($pass) > 0)
		{
			foreach($pass as $one)
			{
				if($box == self::find($one->id_product)->numbox)
				{
					$selected[] = self::remapProductAttributes($one->id_product);
				}
			}
		}

		if(count($obsolete) > 0)
		{
			foreach($obsolete as $one)
			{
				if($box == self::find($one->id_product)->numbox)
				{
					$selected[] = self::remapProductAttributes($one->id_product);
				}
			}
		}

		return $selected;
	}


	public static function add($verb) //replace pmanage_createProduct()
	{
		$all = $verb->all();
		$raw = $verb->except('reference', 'tag', 'fordevicewith', 'price', 'quantity', 'supplier', 'fordevice', 'feature', 'active', 'name', '_token', 'unit_test'); 
		if(count($all) < 13)
		{
			return ['success' => FALSE, 'error' => 'Some basics informations can\'t be left as empty'];
		}
		//ps properties
		$otherData = $verb->only('active', 'price', 'name');
		$id = PS::newProduct($otherData['name'], $raw['type'], $raw['subtype'], $otherData);

		//apb properties
		$product = self::find($id);
		$use_id = ['brand', 'collection', 'type', 'subtype', 'color', 'pattern', 'material'];
		foreach($raw as $k => $v)
		{	
			if(in_array($k, $use_id))
			{
				$field = 'id_'.$k;
				$product->$field = $v;
			}
			else
			{
				$product->$k = $v;
			}			
		}
		$product->save();

		//other apb properties
		$other = ['tag', 'fordevicewith', 'price', 'quantity', 'supplier', 'fordevice', 'feature', 'active'];
		foreach($other as $key)
		{
			if($verb->has($key))
				PB::product(['id' => $id, 'value' => $verb->input($key)], 'set_'.$key);
		}

		$created_data = self::wsOne($id);

		if(isset($all['unit_test']))
		{
			DB::table(self::getProp('product_ps_table'))->where('id_product', '=', $id)->delete();
			self::destroy($id);
		}	

		return $created_data;
	}

	public static function edit($verb)
	{
		$all = $verb->except('_token');
		if(!isset($all['id_product']))
		{
			return ['success' => FALSE, 'error' => 'id_product must set'];
		}

		if(count($all) < 2)
		{
			return ['success' => FALSE, 'error' => 'You need at least 1 attribute to update'];
		}

		$use_id   = ['brand', 'collection', 'type', 'subtype', 'color', 'pattern', 'material'];
		//If you wanna add another column make sure that there's a method for that at PB::product() and/or Product class
		$editable = ['id_product', 'name', 'suppliername', 'brand', 'supplier', 'type', 'subtype', 'pattern', 'collection', 'color', 'material', 'price_reseller', 'price', 'fordevice'];
		//you can add another column only if there's a SET option for your column at PB::product($id, 'set_column') 
		$other    = ['fordevice'];
		$error 	  = [];
		foreach($all as $k => $v)
		{
			if($k != 'unit_test')
				if(!in_array($k, $editable))
				{
					$error[] = $k;
				}
		}

		if(!empty($error))
		{
			return ['success' => FALSE, 'error' => 'Only the following column can be updated', 'column' => $editable];
		}

		if(isset($all['unit_test']))
		{
			return ['unit_test' => 'success'];
		}

		$ps_column   = PS::getFullSchema();

		foreach($all as $k => $v)
		{
			if(in_array($k, $ps_column))
			{
				if($k != 'id_product')
				{
					$data[$k] = $v;
					$error = PS::updateProduct($all['id_product'], $data);
				}
					
			}
		}

		$product = self::find($all['id_product']);
		foreach($all as $k => $v)
		{
			if(in_array($k, $use_id))
			{
				$field = 'id_'.$k;
				$product->$field = $v;
			}
		}
		$product->save();

		$other = ['tag', 'fordevicewith', 'price', 'quantity', 'supplier', 'fordevice', 'feature', 'active'];
		foreach($other as $key)
		{
			if($verb->has($key))
				PB::product(['id' => $all['id_product'], 'value' => $all[$key]], 'set_'.$key);
		}			

		return self::wsOne($all['id_product']);
	}

	public static function removeQuantity($product, $quantity)
	{
		$available = Stock::get($product, 'available');
		$available = $available - $quantity;
		if($available < 0)
			$available = 0;

		Stock::set($product, $available);
		return ['stock' => Stock::get($product)];
	}		

	public static function sendInfos($postData)
	{
		//
		if(!self::find($postData['id_product']))
		{
			return ['success' => FALSE, 'error' => 'product '.$postData['id_product'].' not found']; 
		}

		$data 				= self::remapProductAttributes($postData['id_product']);
		$brandHtmlNname 	= str_slug($data->forbrand['name']);
		$productHtmlName 	= str_slug($data->name);
		$productId 			= $data->id;
		$base_url 			= 'http://www.touchiz.fr/p/';
		$url 				= $base_url.$brandHtmlNname.'/'.$productHtmlName.'-'.$productId.'.html';
		$record 			= ['mail' => $postData['mail'], 'id_product' => $postData['id_product'], 'url' => $url];
		$state 				= PB::recordInfoMail($record);

		if($state)
		{
			return ['success' => FALSE, 'error' => 'error when saving and/or emailing data'];
		}
		else
		{
			return ['success' => TRUE, 'error' => ''];
		}
	}


	/**
	* Internal method
	*/

	protected static function remapProductAttributes($id, $vmode = 'obj')
	{
		$data['id'] 				= $id;
		$data['id_product'] 		= $id;
		$data['name'] 				= PS::product($id, 'PTL.name');
		$data['description'] 		= PS::product($id, 'PTL.description');
		$data['reference'] 			= PS::product($id, 'PT.reference');
		$data['ean'] 				= PS::product($id, 'PT.ean13');
		$data['type'] 				= self::linkTypeFull($id);
		$data['subtype'] 			= PB::product(self::find($id)->id_subtype, 'subtype');
		$data['url'] 				= str_replace('PRODUCT_ID', $id, Config::get('constants.PROD_BASE_URL').self::$url);
		$data['material'] 			= self::linkMaterial($id);
		$data['color'] 				= PB::product($id, 'color');
		$data['pattern'] 			= self::linkPattern($id);
		$data['active'] 			= (PS::product($id, 'PT.active')) ? ['value' => 1, 'display' => 'yes'] : ['value' => 0, 'display' => 'none'];
		$data['obsolete'] 			= (self::find($id)->is_obsolete)  ? ['value' => 1, 'display' => 'yes'] : ['value' => 0, 'display' => 'no'];
		$data['check'] 				= (self::find($id)->is_check) 	  ? ['value' => 1, 'display' => 'yes'] : ['value' => 0, 'display' => 'no'];
		$data['fordevice'] 			= PB::product($id, 'fordevice');
		$data['picture1'] 			= self::getPicture($id, 'picture1', ['len' => 7, 'key' => 'picture']);
		$data['picture2'] 			= self::getPicture($id, 'picture2', ['len' => 7, 'key' => 'picture']);
		$data['picture3'] 			= self::getPicture($id, 'picture3', ['len' => 7, 'key' => 'picture']);
		$data['picture4'] 			= self::getPicture($id, 'picture4', ['len' => 7, 'key' => 'picture']);
		$data['picture5'] 			= self::getPicture($id, 'picture5', ['len' => 7, 'key' => 'picture']);
		$data['picture6'] 			= self::getPicture($id, 'picture6', ['len' => 7, 'key' => 'picture']);
		$data['picture7'] 			= self::getPicture($id, 'picture7', ['len' => 7, 'key' => 'picture']);
		$data['picture8'] 			= self::getPicture($id, 'picture8', ['len' => 7, 'key' => 'picture']);
		$data['picture9'] 			= self::getPicture($id, 'picture9', ['len' => 7, 'key' => 'picture']);
		$data['spicture1']			= self::linkPictureStatus($id, 'spicture1');
		$data['spicture2']			= self::linkPictureStatus($id, 'spicture2');
		$data['spicture3']			= self::linkPictureStatus($id, 'spicture3');
		$data['spicture4']			= self::linkPictureStatus($id, 'spicture4');
		$data['spicture5']			= self::linkPictureStatus($id, 'spicture5');
		$data['spicture6']			= self::linkPictureStatus($id, 'spicture6');
		$data['spicture7']			= self::linkPictureStatus($id, 'spicture7');
		$data['spicture8']			= self::linkPictureStatus($id, 'spicture8');
		$data['spicture9']			= self::linkPictureStatus($id, 'spicture9');
		$data['tag'] 				= PB::product($id, 'tag');
		$data['brand'] 				= self::linkBrand($id);
		$data['fordevicewith'] 		= PB::product($id, 'fordevicewith');
		$data['commentpicture'] 	= (self::find($id)->commentpicture) ? ['value' => 1, 'display' => self::find($id)->commentpicture] : ['value' => '', 'display' => ''];
		$data['price'] 				= ['value' => PS::product($id, 'PT.price'), 'display' => (((int)(PS::product($id, 'PT.price') * 1.2 * 100000))/100000)];
		$data['pricettc'] 			= number_format(floatval($data['price']['value'])*1.2, 2); 
		$data['quantity'] 			= Stock::get($id, 'available');
		$data['date'] 				= PS::product($id, 'PT.date_add');
		$data['width'] 				= ['value' => PS::product($id, 'PT.width'),  'display' => PS::product($id, 'PT.width')];
		$data['height'] 			= ['value' => PS::product($id, 'PT.height'), 'display' => PS::product($id, 'PT.height')];
		$data['weight'] 			= ['value' => PS::product($id, 'PT.weight'), 'display' => PS::product($id, 'PT.weight')];
		$data['depth'] 				= ['value' => PS::product($id, 'PT.depth'),  'display' => PS::product($id, 'PT.depth')];
		$data['numbox'] 			= (self::find($id)->numbox) ? ['value' => self::find($id)->numbox, 'display' => self::find($id)->numbox, 'quantity' => Stock::fromBox(self::find($id)->numbox)] : ['value' => '', 'display' => '', 'quantity' => 0];
		$data['suppliername'] 		= (self::find($id)->suppliername) ? ['value' => self::find($id)->suppliername, 'display' => self::find($id)->suppliername] : ['value' => '', 'display' => ''];
		$data['ppic1'] 				= self::getPicture($id, 'ppic1', ['len' => 4, 'key' => 'ppic']);
		$data['ppic2'] 				= self::getPicture($id, 'ppic2', ['len' => 4, 'key' => 'ppic']);
		$data['externlink'] 		= (self::find($id)->externLink) ? ['value' => self::find($id)->externLink, 'display' => self::find($id)->externLink] : ['value' => '', 'display' => ''];
		$data['sold'] 				= self::getSold($id);
		$data['sold_touchiz'] 		= Order::getSold($id, '', ['date' => '', 'side' => 'touchiz']);
		$data['soldOn30Touchiz'] 	= Order::getSold($id, '', ['date' => date('Y-m-d | H:i:s', strtotime("-1 month")), 'side' => 'touchiz']);
		$data['soldOn60Touchiz'] 	= Order::getSold($id, '', ['date' => date('Y-m-d | H:i:s', strtotime("-2 month")), 'side' => 'touchiz']);
		$data['sold_techtablet'] 	= Order::getSold($id, '', ['date' => '', 'side' => 'techtablet']);
		$data['soldOn30Techtablet']	= Order::getSold($id, '', ['date' => date('Y-m-d | H:i:s', strtotime("-1 month")), 'side' => 'techtablet']);
		$data['soldOn60Techtablet']	= Order::getSold($id, '', ['date' => date('Y-m-d | H:i:s', strtotime("-2 month")), 'side' => 'techtablet']);
		$data['soldOnTouchiz']		= GF::getProductSellingDataForYear($id, date('Y'), 'touchiz');
		$data['soldOnTechtablet']	= GF::getProductSellingDataForYear($id, date('Y'), 'techtablet');		
		$data['sold30'] 			= self::getSold($id, '30');
		$data['sold60'] 			= self::getSold($id, '60');
		$data['wishbuy'] 			= (self::find($id)->wishbuy)  ? ['value' => self::find($id)->wishbuy,  'display' => self::find($id)->wishbuy]  : ['value' => '', 'display' => ''];
		$data['supplier'] 			= (self::find($id)->supplier) ? ['value' => self::find($id)->supplier, 'display' => self::find($id)->supplier] : ['value' => '', 'display' => ''];
		$data['feature'] 			= PB::product($id, 'feature');
		$data['buyingprice'] 		= PB::product($id, 'buyingprice');
		$data['orderedqtty'] 		= PB::product($id, 'orderedqtty');
		$data['manufacturer']		= PB::product($id, 'manufacturer');
		$data['condition']			= self::find($id)->condition;
		//additional field ++
		$data['reseller_description']	= self::find($id)->reseller_description;
		$data['stock']					= Stock::get($id);
		$field_map 						= ['id_collection' => 'id', 'collection_name' => 'name', 'alt_name' => 'alt_name'];
		$data['collection'] 			= self::find($id)->linkCollection($field_map);
		$data['box'] 					= self::find($id)->numbox;
		$price_reseller					= PB::product($id, 'price_reseller');
		$data['price_reseller'] 		= $price_reseller['value'];
		$data['forbrand'] 				= PB::product($data['fordevice']['id'], 'forbrand');
		$data['pictures'] 				= PB::product(['id' => $id, 'type' => 'thumb'], 'cover');
		$data['cover'] 					= PB::product(['id' => $id, 'type' => 'cover'], 'cover');

		if($vmode == 'obj')
			return (object) $data;
		
		return $data;
	}

	protected static function linkPSProduct($custom = FALSE)
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('product_ps_table');
		
		$link = self::join($table_two, $table_one.'.id_product', '=', $table_two.'.id_product');
		if($custom)
			return ['link' => $link, 't1' => $table_one, 't2' => $table_two];

		$link = $link->select($table_one.'.id_product')
		->where($table_one.'.is_obsolete', '=', 0)
		->where($table_one.'.spicture1', '!=', 0);

		return $link;
	}

	protected function linkCollection($field_map = [])
	{
		$link = $this->hasOne('App\Collection', self::$collection_pk, self::$collection_fk)->first();

		if(empty($field_map))
		{
			return $link;
		}
		
		if(count($link) > 0)
		{
			foreach($field_map as $k => $v)
			{
				$collection[$v] = $link->$k;
			}
		}
		else
		{
			foreach($field_map as $k => $v)
			{
				if($k == 'collection_name')
				{
					$collection[$v] = 'divers';
				}
				else
				{
					$collection[$v] = '';
				}				
			}
		}
		
		return $collection;
	}

	protected static function linkTypeFull($id)
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('product_type_table');

		$link = self::join($table_two, $table_one.'.id_type', '=', $table_two.'.id_type')
		->select($table_two.'.type_name', $table_two.'.type_alt', $table_two.'.id_type', $table_two.'.type_one')
		->where($table_one.'.id_product', '=', $id)->first();

		if(count($link) > 0)
		{
			return ['value' => $link->id_type, 'display' => $link->type_name, 'alt' => $link->type_alt, 'displayone' => $link->type_one];
		}
		else
		{
			return ['value' => '', 'display' => '', 'alt' => '', 'displayone' => ''];
		}
	}

	protected static function linkMaterial($id)
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('product_material_table');

		$link = self::join($table_two, $table_one.'.id_material', '=', $table_two.'.id_material')
		->select($table_two.'.material_name', $table_two.'.id_material', $table_two.'.menu')
		->where($table_one.'.id_product', '=', $id)->first();

		if(count($link) > 0)
		{
			return ['value' => $link->id_material, 'display' => $link->material_name, 'menu' => $link->menu];
		}
		else
		{
			return ['value' => '', 'display' => '', 'menu' => ''];
		}
	}

	protected static function linkPattern($id)
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('product_pattern_table');

		$link = self::join($table_two, $table_one.'.id_pattern', '=', $table_two.'.id_pattern')
		->select($table_two.'.pattern_name', $table_two.'.id_pattern', $table_two.'.menu')
		->where($table_one.'.id_product', '=', $id)->first();

		if(count($link) > 0)
		{
			return ['value' => $link->id_pattern, 'display' => $link->pattern_name, 'menu' => $link->menu];
		}
		else
		{
			return ['value' => '', 'display' => '', 'menu' => ''];
		}
	}

	protected static function linkForDevice($device, $type = FALSE)
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('table_fordevice');

		$link = self::join($table_two, $table_one.'.id_product', '=', $table_two.'.id_product')
		->select($table_one.'.id_product')
		->where($table_two.'.id_device', '=', $device);
		if($type)
			$link->where($table_one.'.id_type', '=', $type);

		return $link;
	}

	protected static function getForDevice($device, $for = '')
	{
		$product_linked = self::linkForDevice($device)->get();
		$product_pass 	= [];
		if(count($product_linked) == 0)
			return [];
		
		if($for == 'pmanager')
		{
			foreach($product_linked as $pl) 
			{
				$p = self::wsOne($pl->id_product, 'obj');
				$full_data = self::remapProductAttributes($pl->id_product);
				$p->fullproduct  = [];
				$p->fullproduct  = $full_data;
				$p->selling_info = ['soldOnTouchiz' => $full_data->soldOnTouchiz, 'soldOnTechtablet' => $full_data->soldOnTechtablet];
				$product_pass[]  = $p;
			}
		}
		else
		{
			foreach($product_linked as $pl)
			{
				$wsOne = self::wsOne($pl->id_product, 'obj');
				if($wsOne->quantity && $wsOne->collection['id'])
				{
					$product_pass[] = $wsOne;
				}
				
			}
		}
		

		usort($product_pass, function($a, $b) {
    		$c = strcmp($a->type['value'], $b->type['value']);
    		if($c == 0)
    			return strcmp($a->collection['id'], $b->collection['id']);
    		else 
    			return $c;
		});

		$list 	= [];
		for($i = 0; $i < count($product_pass); $i++) 
		{
			if(!$i || $product_pass[$i]->type["value"] != $product_pass[$i-1]->type["value"]) 
			{
				$currentType 					  = $product_pass[$i]->type["display"];
				$list[$currentType] 			  = [];
				$currentColl 					  = $product_pass[$i]->collection["name"];
				$list[$currentType][$currentColl] = [];
			} 
			elseif ($product_pass[$i]->collection["id"] != $product_pass[$i-1]->collection["id"]) 
			{
				$currentColl 					  = $product_pass[$i]->collection["name"];
				$list[$currentType][$currentColl] = [];
			} 

			$list[$currentType][$currentColl][]   = $product_pass[$i];
		}

		return $list;
	}

	protected static function linkPictureStatus($id, $field)
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('table_spicture');

		$link = self::join($table_two, $table_one.'.'.$field, '=', $table_two.'.id_picturestatus')
		->select($table_two.'.picturestatus_name', $table_two.'.id_picturestatus')
		->where($table_one.'.id_product', '=', $id)->first();

		if(count($link) > 0)
		{
			return ['value' => $link->id_picturestatus, 'display' => $link->picturestatus_name];
		}
		else
		{
			return ['value' => '', 'display' => ''];
		}
	}

	protected static function linkBrand($id)
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('table_brand');

		$link = self::join($table_two, $table_one.'.id_brand', '=', $table_two.'.id_brand')
		->select($table_two.'.brand_name', $table_two.'.id_brand')
		->where($table_one.'.id_product', '=', $id)->first();

		if(count($link) > 0)
		{
			return ['value' => $link->id_brand, 'display' => $link->brand_name];
		}
		else
		{
			return ['value' => '', 'display' => ''];
		}
	}

	protected static function getSold($id, $sold = '')
	{
		$p = self::find($id);

		if($sold == '30')
		{
			return ['value' => $p->sold30, 'display' => $p->sold30];
		}
		elseif($sold == '60')
		{
			return ['value' => $p->sold60, 'display' => $p->sold60];
		}
		elseif($sold == '')
		{
			return ['value' => $p->sold, 'display' => $p->sold];
		}
		else
		{
			return Order::getSold($id, $sold);
		}
	}

	protected static function prepareBoxCandidate()
	{
		$pass 		= self::linkPSProduct()->orderBy(self::getProp('table').'.id_product', 'desc')->get();
		$model  	= self::linkPSProduct(TRUE);
		$obs 		= $model['link']->select($model['t1'].'.id_product')
		->where($model['t1'].'.is_obsolete', '=', 1)
		->where($model['t2'].'.active', '=', 1)
		->orderBy($model['t2'].'.id_product', 'desc')->get();

		return ['pass' => $pass, 'obs' => $obs];
	}

	protected static function getPicture($id, $attribute, $param, $type = 'source') 
	{
		if(!substr_compare($attribute, $param['key'], 0, $param['len'])) 
		{
			$index = substr($attribute, $param['len']);
			if($index < 1 || $id < 0) 
			{
				return "";
			}

			$urls = self::preparePictureUrl($id, $type);
			
			if (empty($urls) || !isset($urls[$index - 1])) 
			{
				return "";
			} 
			else 
			{
				return $urls[$index - 1];
			}
		}

		return "";
	}

	protected static function preparePictureUrl($id, $type) 
	{	
		if(empty($id))
			return null;

		$digits = str_split($id);
		$path = "/pic/".$type."/";

		foreach($digits as $d) 
		{
			$path.= $d."/";
		}

		$urls = [];
		for ($i=1; $i<=9; $i++) 
		{
			$file = $path.$i.".jpg";
			if(file_exists("/var/www/prestashop".$file)) 
			{
				$urls[$i - 1] = "http://".$_SERVER['SERVER_NAME'].$file;
				
			} else 
			{
				$urls[$i - 1] = "";
			}
		}
		
		for ($i=1; $i<=9; $i++) 
		{
			if (empty($url[$i])) 
			{
				//
			}
		}

		return $urls;
	}

}
