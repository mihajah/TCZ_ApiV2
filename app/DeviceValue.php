<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceValue extends Model {

	//
	protected $table 		= 'apb_devices_values';
	protected $primaryKey 	= 'id_value';

	public function getAll()
	{
		//
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) 	[
										'id_value' 	=> $one->id_value, 
										'value' 	=> $one->value, 
										'attribute' => $one->attribute
									];
			}
		}

		return $data;
	}

}
