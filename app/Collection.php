<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;

class Collection extends Model {

	//
	protected $table 	      	= 'apb_collections';
	protected $primaryKey     	= 'id_collection';
	protected $table_types    	= 'apb_types';
	protected $table_subtypes 	= 'apb_subtypes';
	protected $table_materials	= 'apb_materials';
	protected $table_patterns	= 'apb_patterns';
	protected $table_features	= 'apb_features';
	protected $table_color		= 'apb_colors';
	protected $table_dc			= 'apb_collections_defaultcolor';
	protected $table_csv		= 'apb_customers_csv';
	protected $fillable			= ['collection_name', 'alt_name', 'id_supplier', 'id_brand', 'price', 'price_touchiz', 'forDeviceType', 'type',
									'subtype', 'material', 'pattern', 'feature1', 'feature2', 'feature3', 'feature4', 'feature5',
									'classic', 'DefaultColors', 'pcv'];

	use ModelGetProperties;

	/**
	* ws Method
	*/
	public static function wsOne($id)
	{
		return self::getFullSchema($id);
	}

	public static function wsAll()
	{
		$all 			= [];
		$collectionId 	= self::getAllId();
		foreach($collectionId as $one)
		{
			$all[] = self::wsOne($one);
		}

		return $all;
	}

	public static function wsAdd($verb)
	{
		$raw 		= $verb->except('unit_test');
		$fillable 	= self::getProp('fillable');
		$fail 		= FALSE;

		if(count($raw) != count($fillable))
		{
			$fail = TRUE;
		}

		foreach($fillable as $key)
		{
			if(!$verb->has($key))
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Only those column can be added', 'column' => $fillable];
		}

		$data 		= $verb->except('DefaultColors', 'pcv', 'unit_test');
		$insert_id 	= self::add($data);

		$dfc = $verb->input('DefaultColors');
		$pcv = $verb->input('DefaultColors');

		if(is_array($dfc) && count($dfc) > 0)
		{
			foreach($dfc as $color)
			{
				self::addDefaultColor($insert_id, $color);
			}
		}
		
		if(is_array($pcv) && count($pcv) > 0)
		{
			foreach($verb->input('pcv') as $pc)
			{
				self::addPcv($insert_id, $pc);
			}
		}
		

		$freshFull = self::wsOne($insert_id);

		if($verb->has('unit_test'))
		{
			self::destroy($insert_id);
			DB::table(self::getProp('table_dc'))->where('id_collection', '=', $insert_id)->delete();
		}

		return $freshFull;
	}

	public static function wsEdit($verb)
	{
		$fail 		= FALSE;
		$raw 		= $verb->except('unit_test');
		$editable 	= self::getProp('fillable');
		$editable[] = 'id_collection';

		if(count($raw) != count($editable))
		{
			$fail = TRUE;
		}

		foreach($raw as $k => $v)
		{
			if(!in_array($k, $editable))
			{
				$fail = TRUE;
			}	
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Only those column can be updated', 'column' => $editable];
		}

		$data = $verb->except('unit_test', 'DefaultColors', 'pcv');
		self::edit($data);		

		$dc  = $verb->input('DefaultColors');
		$pcv = $verb->input('pcv');

		if(is_array($dc) && count($dc) > 0)
		{
			self::removeAttr($data['id_collection'], 'defaultColor');

			foreach($dc as $color)
			{
				self::addDefaultColor($data['id_collection'], $color);
			}
		}

		if(is_array($pcv) && count($pcv) > 0)
		{
			self::removeAttr($data['id_collection'], 'pcv');

			foreach($pcv as $pc)
			{
				self::addPcv($data['id_collection'], $pc);
			}
		}

		return ['success' => true, 'data' => self::wsOne($data['id_collection'])];		
	}

	/**
	* Public method
	*/
	public static function add($data)
	{
		$fresh = self::create($data);
		return $fresh->id_collection;
	}

	public static function edit($raw)
	{
		$data = [];
		foreach($raw as $k => $v)
		{
			if($k != 'id_collection')
				$data[$k] = $v;
		}

		$return = self::where('id_collection', '=', $raw['id_collection'])->update($data);
		DB::table(self::getProp('table_dc'))->where('id_collection', '=', $raw['id_collection'])->delete();
		return $return;
	}	

	public static function getAllId()
	{
		$collection = [];
		$result 	= self::select('id_collection')->get();
		if(count($result) == 0)
		{
			return [];
		}

		foreach($result as $id)
		{
			$collection[] = $id->id_collection;
		}

		return $collection;
	}

	public static function getFullSchema($id, $display = 'both')
	{
		$full = self::remapCollectionAttributes($id, 'arr');

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

	public static function getDefaultColor($id, $needed = 'id_color')
	{
		$data 	= [];
		$result = DB::table(self::getProp('table_dc'))->where('id_collection', '=', $id)->get();
		if(count($result) == 0)
		{
			return [];
		}

		foreach($result as $one)
		{
			if($needed == 'id_color')
			{
				$data[] = $one->id_color;
			}
			else
			{
				$data[] = ['id_assignement' => $one->id_value, 'colorinfo' => self::getOneColor($one->id_color)];
			}
			
		}

		return $data;
	}

	public static function getOneColor($id)
	{
		$data 	= [];
		$result = DB::table(self::getProp('table_color'))->where('id_color', '=', $id)->get();
		if(count($result) == 0)
		{
			return [];
		}

		foreach($result as $color)
		{
			$data[] = [
						'id' 	=> $color->id_color,
						'name' 	=> $color->color_name,
						'ref' 	=> $color->ref_color,
						'code'	=> $color->code
					  ];
		}

		return $data;
	}

	public static function getTmpPicture($id)
	{
		$result     = [];
		$colorLists = self::getDefaultColor($id);
		foreach($colorLists as $color)
		{
			$colorfull = self::getOneColor($color);
			$name = $colorfull[0]['name'];
			for ($i=1; $i<=9; $i++) 
			{
				$picture="http://".$_SERVER['SERVER_NAME']."/pic/collections/".$id."_".$color."_".$i.".jpg";
				$urltotest = "/var/www/prestashop/pic/collections/".$id."_".$color."_".$i.".jpg";
					if(file_exists($urltotest))
					{
						$result[$name]['picture_'.$i] = $picture;
					}
					else
					{
						$result[$name]['picture_'.$i] = '';
					}				
			}
		}

		return $result;
	}

	public static function getPvc($id)
	{
		$table_csv = self::getProp('table_csv');
		$sql       = "SELECT S.* FROM ".$table_csv." AS S 
						WHERE S.id_collection = :c 
						AND S.id_product = 0
						ORDER BY S.id_customer ASC";

		$result    = DB::select($sql, ['c' => $id]);

		if(count($result) == 0)
		{
			return [];
		}

		$pcv = [];

		foreach($result as $one)
		{
			$pcv[] = ['customer' => $one->id_customer, 'price' => $one->price];
		}

		return $pcv;
	}

	/**
	* Internal method
	*/

	protected static function removeAttr($collectionId, $attr)
	{
		$DB = '';

		if($attr == 'defaultColor')
		{
			$table = self::getProp('table_dc');
			$DB    = DB::table($table)->where('id_collection', '=', $collectionId);
		}
		else if($attr == 'pcv')
		{
			$table = self::getProp('table_csv');
			$DB    = DB::table($table)->where('id_collection', '=', $collectionId)
			->where('id_product', '=', 0);
		}
		else
		{
			return;
		}

		$DB->delete();
	}

	protected static function addPcv($collectionId, $pcv)
	{
		$table_csv = self::getProp('table_csv');
		$data      = ['id_customer' => $pcv['id_customer'], 'id_collection' => $collectionId, 'price' => $pcv['price'], 'id_product' => 0];

		DB::table($table_csv)->insert($data);
	}

	protected static function addDefaultColor($collectionId, $colorId)
	{
		$table_dc = self::getProp('table_dc');
		$data     = ['id_collection' => $collectionId, 'id_color' => $colorId];

		DB::table($table_dc)->insert($data);
	}

	protected static function remapCollectionAttributes($id, $vmode = 'obj')
	{
		if(!self::find($id))
		{	
			return [];
		}

		$master	  = self::getProp('table');
		$table_on = self::getProp('table_types');
		$table_tw = self::getProp('table_subtypes');
		$table_th = self::getProp('table_materials');
		$table_fo = self::getProp('table_patterns');
		$table_fi = self::getProp('table_features');

		$link = self::leftJoin($table_on, $master.'.type', '=', $table_on.'.id_type')
		->select(
			$master.'.*', 
			DB::raw('
				T.type_display,
				S.subtype_display,
				M.material_name,
				P.pattern_name,
				F1.id_feature AS id_feature1, 
				F1.feature_name AS feature1_name, 
				F2.id_feature AS id_feature2, 
				F2.feature_name AS feature2_name,
				F3.id_feature AS id_feature3, 
				F3.feature_name AS feature3_name,
				F4.id_feature AS id_feature4, 
				F4.feature_name AS feature4_name,
				F5.id_feature AS id_feature5, 
				F5.feature_name AS feature5_name'
		))
		->leftJoin($table_on.' AS T',  $master.'.type',     '=', 'T.id_type')
		->leftJoin($table_tw.' AS S',  $master.'.subtype',  '=', 'S.id_subtype')
		->leftJoin($table_th.' AS M',  $master.'.material', '=', 'M.id_material')
		->leftJoin($table_fo.' AS P',  $master.'.pattern',  '=', 'P.id_pattern')
		->leftJoin($table_fi.' AS F1', $master.'.feature1', '=', 'F1.id_feature')
		->leftJoin($table_fi.' AS F2', $master.'.feature2', '=', 'F2.id_feature')
		->leftJoin($table_fi.' AS F3', $master.'.feature3', '=', 'F3.id_feature')
		->leftJoin($table_fi.' AS F4', $master.'.feature4', '=', 'F4.id_feature')
		->leftJoin($table_fi.' AS F5', $master.'.feature5', '=', 'F5.id_feature')
		->where($master.'.id_collection', '=', $id)->first();

		if(count($link) == 0)
		{
			return [];
		}

		$features = [];
		for($i=1; $i<6; $i++)
		{	
			$f_id 	= 'feature'.$i;
			$f_name = $f_id.'_name';

			if($link->$f_id > 0)				
				$features[] = ['id' => $link->$f_id, 'name' => $link->$f_name];
		}

		$data 						= [];
		$data['id'] 				= $link->id_collection;
		$data['name']				= $link->collection_name;
		$data['altname']			= $link->alt_name;
		$data['supplier']			= $link->id_supplier;
		$data['devicetype']			= ($link->forDeviceType) ? 'Phone' : 'Tablet';
		$data['type']				= ['id' => $link->type, 'name' => $link->type_display];
		$data['subtype']			= ['id' => $link->subtype, 'name' => $link->subtype_display];
		$data['material']			= ['id' => $link->material, 'name' => $link->material_name];
		$data['pattern']			= ['id' => $link->pattern, 'name' => $link->pattern_name];
		$data['price']				= $link->price;
		$data['price_tcz']			= $link->price_touchiz;
		$data['features']			= $features;
		$data['classic']			= ['value' => $link->classic, 'display' => ($link->classic) ? 'Yes' : 'No'];
		$data['defaultColor']		= self::getDefaultColor($id, 'assignement');
		$data['temp_picture']		= self::getTmpPicture($id);
		$data['brand']				= $link->id_brand;
		$data['pcv_ttc']            = self::getPvc($id);

		if($vmode == 'obj')
			return (object) $data;

		return $data;

	}
}
