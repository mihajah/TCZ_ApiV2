<?php
namespace App\Helpers;
use DB;
use App\Traits\ModelGetProperties;
use App\DeviceGroup;
use App\Color;

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
	}

}
?>