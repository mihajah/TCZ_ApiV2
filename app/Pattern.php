<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Pattern extends Model {

	//
	protected $table 		= 'apb_patterns';
	protected $primaryKey 	= 'id_pattern';

	public function getAll()
	{
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) 	[
										'id' 			=> $one->id_pattern, 
										'name' 			=> $one->pattern_name, 
										'supplier_name' => $one->supplier_name,
										'menu' 			=> $one->menu
									];
			}
		}

		return $data;
	}
}
