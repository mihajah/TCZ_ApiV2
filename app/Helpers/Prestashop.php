<?php
namespace App\Helpers;
use DB;
use App\Product;

class Prestashop
{
	protected static $product_table_lang = 'ps_product_lang';
	protected static $product_table 	 = 'ps_product';
	protected static $stock_table		 = 'ps_stock_available';

	/**
	* Product
	*/
	public static function product($id_ean, $field = 'all')
	{
		$and   = ' PT.id_product = :id';
		$where = ['id' => $id_ean];

		if(strlen($id_ean) == 12 || strlen($id_ean) == 13)
		{
			$and = ' PT.ean13 = :ean';
			$where = ['ean' => $id_ean];
		}

		if($field != 'all')
		{
			$sql = 'SELECT '.$field.' FROM '.self::$product_table.' AS PT, '.self::$product_table_lang.' AS PTL 
					WHERE PT.id_product = PTL.id_product
					AND '.$and;
			$results = DB::select($sql, $where);
			$field   = str_replace(['PT.', 'PTL.'], ['', ''], $field);
			$data[]  = $results[0]->$field;
		}
		else
		{
			$sql = 'SELECT * FROM '.self::$product_table.' AS PT, '.self::$product_table_lang.' AS PTL 
					WHERE PT.id_product = PTL.id_product
					AND '.$and;
			$data = DB::select($sql, $where);
		}

		if(count($data) > 0)
		{
			return $data[0];
		}
		else
		{
			return false;
		}
		
	}

	public static function newProduct($name, $type, $subType, $otherData, $id = NULL)
	{
		if($id)
		{
			if(!Product::find($id))
			{
				return false;
			}	

			DB::table('apb_prd')->insert(['id_product' => $id]);
			return true;
		}
		else
		{
			$idType = self::generateCategory(['value' => $type, 'type' => 'type']);
			$idSubType = self::generateCategory(['value' => $subType, 'type' => 'subtype', 'idType' => $idType]);

			$psp  = [
						'price' 					=> (((int)($otherData['price']*5.0/6.0*100000))/100000), 
						'id_tax_rules_group'		=> 1, 
						'id_manufacturer'			=> 0, 
						'id_supplier' 				=> 0, 
						'quantity'					=> 0, 
						'minimal_quantity'			=> 1, 
						'additional_shipping_cost'  => 0,
						'wholesale_price'			=> 0,
				    	'ecotax'					=> 0, 
				    	'width'						=> 0, 
				    	'height'					=> 0, 
				    	'depth'						=> 0, 
				    	'weight'					=> 0, 
				    	'out_of_stock'				=> 0, 
				    	'active'					=> ($otherData['active']) ? 1 : 0, 
				    	'id_category_default'		=> $idSubType, 
				    	'available_for_order'		=> 1, 
				    	'show_price'				=> 1,
				    	'on_sale'					=> 0, 
				    	'online_only'				=> 0
				    ];

			$pspl = [
						'id_shop' 					=> 1, 
						'id_lang'					=> 1, 
						'name'						=> $name, 
						'meta_keywords'				=> $name, 
						'description_short'			=> '', 
						'link_rewrite'				=> str_slug($name, '-')
					];

			$id = DB::table(self::$product_table)->insertGetId($psp);
			DB::update('UPDATE '.self::$product_table.' SET reference = ? WHERE id_product = ?', [self::generateReference($id, $type, $subType), $id]);
			$pspl['id_product'] = $id;
			DB::table(self::$product_table_lang)->insert($pspl);
			self::addToCategories([$idSubType], $id);

			if(!Product::find($id))
			{
				DB::table('apb_prd')->insert(['id_product' => $id]);
			}

			return $id;
		}
	}

	public static function updateProduct($id, $candidate)
	{
		$field_one = self::getFullSchema('first');
		$field_two = self::getFullSchema('second');
		$error 	   = [];

		if(count($candidate) > 0)
		{
			$data = [];
			foreach($candidate as $k => $v)
			{
				
				if(in_array($k, $field_one))
				{
					$data[$k] = $v;
					DB::table(self::$product_table)->where('id_product', '=', $id)->update($data);
				}
				else
				{
					$error[] = $data;
				}
			}
		}
		
		if(count($candidate) > 0)
		{
			$data = [];
			foreach($candidate as $k => $v)
			{
				if(in_array($k, $field_two))
				{
					if($k == 'name')
					{
						$data['link_rewrite'] =  str_slug($v, '-');
						$data['meta_keywords'] = $v;
						$data[$k] = $v;
					}
					else
					{
						$data[$k] = $v;
					}

					DB::table(self::$product_table_lang)->where('id_product', '=', $id)->update($data);
				}
				else
				{
					$error[] = $data;
				}
			}
		}
		

		return $error;
	}

	public static function getFullSchema($display = 'both')
	{
		$field_one = DB::getSchemaBuilder()->getColumnListing(self::$product_table);
		$field_two = DB::getSchemaBuilder()->getColumnListing(self::$product_table_lang);
		$schema = [];

		if($display == 'both')
		{
			foreach($field_one as $column)
			{
				$schema[$column] = $column;
			}

			foreach($field_two as $column)
			{
				$schema[$column] = $column;
			}

			return $schema;
		}

		if($display == 'first')
		{
			foreach($field_one as $column)
			{
				$schema[$column] = $column;
			}

			return $schema;
		}

		if($display == 'second')
		{
			foreach($field_two as $column)
			{
				$schema[$column] = $column;
			}

			return $schema;
		}
	}

	/**
	* Give a new position to a new added product
	*/
	protected static function addToCategories($categories = array(), $thisId)
	{
		if (empty($categories))
			return false;

		if (!is_array($categories))
			$categories = array($categories);

		if (!count($categories))
			return false;

		$categories = array_map('intval', $categories);

		$current_categories = self::getProductCategories($thisId);
		$current_categories = array_map('intval', $current_categories);

		// for new categ, put product at last position
		$res_categ_new_pos = Db::select('
			SELECT id_category, MAX(position)+1 newPos
			FROM `ps_category_product`
			WHERE `id_category` IN('.implode(',', $categories).')
			GROUP BY id_category');
		foreach ($res_categ_new_pos as $array)
			$new_categories[(int)$array->id_category] = (int)$array->newPos;

		$new_categ_pos = array();
		foreach ($categories as $id_category)
			$new_categ_pos[$id_category] = isset($new_categories[$id_category]) ? $new_categories[$id_category] : 0;

		$product_cats = array();

		foreach ($categories as $new_id_categ)
			if (!in_array($new_id_categ, $current_categories))
				$product_cats[] = array(
					'id_category' => (int)$new_id_categ,
					'id_product' => (int)$thisId,
					'position' => (int)$new_categ_pos[$new_id_categ],
				);

		DB::table('ps_category_product')->insert($product_cats);
		return true;
	}

	/**
	* Get product category by Id
	*/
	protected static function getProductCategories($id_product = '')
	{
		$ret = array();

		$row = Db::select('
			SELECT `id_category` FROM `ps_category_product`
			WHERE `id_product` = '.(int)$id_product
		);

		if ($row)
			foreach ($row as $val)
				$ret[] = $val->id_category;

		return $ret;
	}

	/**
	* Sync apb_type, apb_subtype AND ps_category, ps_category_lang 
	* Must have the same value
	* apb table as master, ps table as slave
	*/
	protected static function generateCategory($param)
	{
		$name = self::getValueFromIndex($param['value'], $param['type']);
		$cat = self::getSimpleCategories(1);
		$found = -1;
		for($i=0; $i<count($cat); $i++)
		{
			if(strtolower($cat[$i]->name) == strtolower($name))
			{
				$found = $i;
				break;
			}
		}

		if($found < 0)
		{
			$active    = 0;
			$id_parent = 2;
			if($param['type'] == 'subtype')
			{
				$active = 1;
				$id_parent = $param['idType'];
			}
				

			$to = ['id_parent' => $id_parent, 'active' => $active];
			$tt = ['name' => $name, 'link_rewrite' => str_slug($name, '-')];
			$id = self::newCategory($to, $tt);
		}
		else
		{
			$id = $cat[$found]->id_category;
		}

		return $id;
	}

	protected static function getValueFromIndex($index, $attribute) 
	{
		$sql = "SELECT P.".$attribute."_name FROM apb_".$attribute."s as P WHERE P.id_".$attribute." = ".$index;
		$res = DB::select($sql);
		if(count($res) == 0) 
		{
			echo "Error : getValueFromIndex didn't find anything, you need to add id = ".$index." with all its own data inside apb_".$attribute;
			return '';
			exit;
		}

		$field = $attribute.'_name';

		return $res[0]->$field;
	}

	protected static function getSimpleCategories($id_lang)
	{
		$sql = 'SELECT c.`id_category`, cl.`name` FROM `ps_category` c 
				LEFT JOIN `ps_category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.id_shop = 1 ) 
				INNER JOIN ps_category_shop category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = 1) 
				WHERE cl.`id_lang` = '.$id_lang.' AND c.`id_category` != 1 
				GROUP BY c.id_category 
				ORDER BY c.`id_category`, category_shop.`position`';
		return DB::select($sql);
	}

	protected static function newCategory($to, $tt)
	{
		$to['date_add'] = date('Y-m-d H:i:s');
		$to['date_upd'] = date('Y-m-d H:i:s');
		$id = DB::table('ps_category')->insertGetId($to);
		$tt['id_category'] = $id;
		DB::table('ps_category_lang')->insert($tt);

		return $id;
	}

	protected static function generateReference($id, $type, $subtype) 
	{
		$t = dechex($type);
		if (strlen($t) == 1) {
			$t = "0".$t;
		} else if (strlen($t) > 2) {
			print "error, type id too long";
		}
		$s = dechex($subtype);
		if (strlen($s) == 1) {
			$s = "00".$s;
		} else if (strlen($s) == 2) {
			$s = "0".$s;
		} else if (strlen($s) > 3) {
			print "error, subtype id too long";
		}
		$i = dechex($id);
		if (strlen($i) == 1) {
			$i = "0000".$i;
		} else if (strlen($i) == 2) {
			$i = "000".$i;
		} else if (strlen($i) == 3) {
			$i = "00".$i;
		} else if (strlen($i) == 4) {
			$i = "0".$i;
		} else if (strlen($i) > 5) {
			print "error, id too long";
		}
		
		return $t."-".$s."-".$i;
	}


}
?>