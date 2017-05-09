<?php
namespace App\Helpers;
use DB;
use App\Traits\ModelGetProperties;
use App\DeviceGroup;
use App\Color;
use App\Type;
use App\Material;
use App\Feature;
use App\SubType;
use App\Pattern;

class Property {

	use ModelGetProperties;

	public static function set($name)
	{
		if($name == 'deviceGroup')
		{
			$DeviceGroup = new DeviceGroup;
			return $DeviceGroup;
		}

		if($name == 'color')
		{
			$Color = new Color;
			return $Color;
		}

		if($name == 'type')
		{
			$Type = new Type;
			return $Type;
		}

		if($name == 'material')
		{
			$Material = new Material;
			return $Material;
		}

		if($name == 'feat')
		{
			$Feature = new Feature;
			return $Feature;
		}

		if($name == 'subtype')
		{
			$SubType = new SubType;
			return $SubType;
		}

		if($name == 'pattern')
		{
			$Pattern = new Pattern;
			return $Pattern;
		}
	}

}
?>