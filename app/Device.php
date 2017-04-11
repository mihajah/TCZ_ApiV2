<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use App\Stock;
use DB;

class Device extends Model {

	//
	protected $table 	  		= 'apb_devices';
	protected $table_dg			= 'apb_devices_groups';
	protected $table_dv			= 'apb_devices_values';
	protected $primaryKey 		= 'id_device';
	protected $fillable			=  ['name', 'brand', 'id_group', 'os', 'type', 'screen_size', 'code_reference', 'main_connector', 'video_output',
					 				'external_storage', 'bluetooth', 'nfc', 'ant', 'alternative_names', 'full_references'];

	use ModelGetProperties;

	/**
	* ws Method
	*/
	public static function wsAll()
	{
		return self::getAllDevice();
	}

	public static function wsOne($id)
	{
		return self::getFullSchema($id);
	}

	public static function wsAllByBrand($id, $type = '', $ignore = '')
	{
		return self::getAllDevice($id, $type, $ignore);
	}

	public static function wsAllBrand()
	{
		return self::getAllBrand();
	}

	public static function wsAdd($verb)
	{
		$empty 		= FALSE;
		$fillable   = self::getProp('fillable');

		foreach($fillable as $field)
		{
			if(!$verb->has($field))
			{
				$empty = TRUE;
			}
		}

		if($empty)
		{
			return ['empty' => $empty, 'error' => $fillable];
		}

		$data = $verb->except('unit_test');
		$new_data = self::add($data);

		if($verb->has('unit_test'))
		{
			self::destroy($new_data['id']);
		}

		return ['success' => TRUE, 'new_data' => $new_data];
	}

	public static function wsEdit($verb)
	{
		$empty 		= FALSE;
		$fillable   = self::getProp('fillable');
		$fillable[] = 'id_device';


		foreach($fillable as $field)
		{
			if(!$verb->has($field))
			{
				$empty = TRUE;
				//echo 'tsis '.$field;
			}
			else
			{
				//echo $field.'<br />';
			}
		}

		if($empty)
		{
			return ['missing' => $empty, 'error'=> 'Only those column can be updated', 'column' => $fillable];
		}

		$data = $verb->all();
		return self::edit($data);
	}

	/**
	* Public method
	*/

	public static function edit($raw)
	{
		//
		$data = [];
		foreach($raw as $k => $v)
		{
			if($k != 'id_device')
				$data[$k] = $v;
		}
		return self::where('id_device', '=', $raw['id_device'])->update($data);
	}

	public static function add($data)
	{
		$insert_id = self::create($data);
		return self::wsOne($insert_id->id_device);
	}

	public static function getAllBrand()
	{
		$brand = DB::table(self::getProp('table_dv'))->where('attribute', '=', 'brand')
		->orderBy('popular', 'desc')->get();

		return $brand;
	}

	public static function getAllDevice($brand = '', $type = '', $ignore = '')
	{
		$avd = self::getAvailableDeviceId($brand, $type, $ignore);
		$devices = [];

		for($i=0; $i<count($avd); $i++)
		{
			$temp = self::remapDeviceAttributes($avd[$i]);
			if(!empty($temp))
				$devices[$avd[$i]] = $temp;
		}

		usort($devices, function($a, $b) {
    		$c =  strcmp($a->group['id'], $b->group['id']);
    		if($c == 0) 
    		{
    			$c = intval($b->group['popularity']) - intval($a->group['popularity']);
    			if ($c ==0)
    				return intval($b->popularity) - intval($a->popularity);
    			return $c;
    		}
    		else 
    			return $c;
		});

		$data = [];
		for ($i=0; $i<count($devices); $i++) 
		{
			if (!$i || $devices[$i]->group["id"] != $devices[$i-1]->group["id"]) 
			{
				$currentGroup = $devices[$i]->group["name"];
				$data[$currentGroup] = [];
			} 

			$data[$currentGroup][] = $devices[$i];
		}

		return $data;
	}

	public static function getAvailableDeviceId($brand = '', $type = '', $ignoreProduct = '')
	{
		$devices = [];

		if($brand == '') 
		{
 			$result = self::all();
	 	} 
	 	else if($type == '') 
	 	{
	 		$result = self::where('brand', '=', $brand)->get();
	 	}
	 	else 
	 	{
	 		$result = self::where('brand', '=', $brand)->where('type', '=', $type)->get();
	 	}


		if(count($result) == 0) 
		{
			return $devices;
		} 
		else 
		{
			if($ignoreProduct == '')
			{
				for($i=0; $i<count($result); $i++) 
				{
					$link = Stock::linkForDevice();
					$result2 = $link['link']
					->select(DB::raw('SUM('.$link['t1'].'.quantity) as total'))
					->where($link['t2'].'.id_device', '=', $result[$i]->id_device)->first();

					if(count($result2) > 0 && $result2->total > 0)
						$devices[] = $result[$i]->id_device;
				}
			}
			else
			{
				for($i=0; $i<count($result); $i++)
				{
					$devices[] = $result[$i]->id_device;
				}
			}
			

			return $devices;
		}
	}

	public static function getFullSchema($id, $display = 'both')
	{
		$full = self::remapDeviceAttributes($id, 'arr');

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

	/**
	* Internal method
	*/

	protected static function remapDeviceAttributes($id, $vmode = 'obj')
	{
		//
		$table_one 	 = self::getProp('table');
		$table_two   = self::getProp('table_dg');
		$table_three = self::getProp('table_dv');

		$device = self::leftJoin($table_two, $table_one.'.id_group', '=', $table_two.'.id_group')
		->where($table_one.'.id_device', '=', $id)->first();

		if(count($device) == 0)
		{
			return [];
		}

		$prop    = [];
		$prop[0] = 'none';
		$values  = DB::table($table_three)->get();
		foreach($values as $v)
		{
			$prop[$v->id_value] = $v->value;
		}

		$data 	= [];
		$primary = [
					'brand',
					'type',
					'screen_size',
					'main_connector',
					'video_output',
					'external_storage',
					'bluetooth',
					'os',
					'nfc',
					'ant',
					'url',
					'backurl',
					'miniurl'
				 ];

		foreach($primary as $column)
		{
			if($column == 'url')
			{
				$data[$column] = "/pic/devices/".$id."_f.jpg";
			}	
			else if($column == 'backurl')
			{
				$data[$column] = "/pic/devices/".$id."_b.jpg";
			}
			else if($column == 'miniurl')
			{
				$data[$column] = "/pic/devices/m".$id.".jpg";
			}
			else
			{
				$data[$column] = $prop[$device->$column];
			}			
		}

		$data['id']						= $device->id_device;
		$data['brand'] 					= ['id' => $device->brand, 'name' => $data['brand']]; 
		$data['popularity'] 			= $device->popular;
		$data['name'] 					= $device->name;
		$data['alternative_names'] 		= $device->alternative_names;
		$data['html_name'] 				= $device->html_name;
		$data['code_reference'] 		= $device->code_reference;
		$data['full_reference'] 		= $device->full_references;

		if(!$device->id_group)
		{
			$data['group_name'] 		= 'Autres '.($data['type'] == 'phone' ? 'Téléphones' : 'Tablettes');
		}
		else
		{
			$data['group_name']			= $device->group_name;
		}

		$data['group'] 					= [
											'id' 		 => $device->id_group,
											'name' 		 => $data['group_name'],
											'popularity' => ($device->id_group) ? $device->group_name : 0
										  ];

		$data['pictures'] 				= [
											'front' 	 => 'http://'.$_SERVER['SERVER_NAME'].$data['url'],
											'back'		 => 'http://'.$_SERVER['SERVER_NAME'].$data['backurl'],
											'miniature'	 => 'http://'.$_SERVER['SERVER_NAME'].$data['miniurl']
										  ];			


		if($vmode == 'obj')
			return (object) $data;


		return $data;
	}


}


