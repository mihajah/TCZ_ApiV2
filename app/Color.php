<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Color extends Model {

	//
	protected $table 		= 'apb_colors';
	protected $primaryKey 	= 'id_color';

	public function getAll()
	{
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) 	[
										'id' 			=> $one->id_color, 
										'name' 			=> $one->color_name, 
										'ref' 			=> $one->ref_color,
										'code' 			=> $one->code,
										'color_alt1' 	=> $one->color_alt1,
										'name_eng'		=> $one->name
									];
			}
		}

		return $data;
	}

	public function store($data)
	{
		$result = self::all();
		$exist 	= FALSE;
		foreach($result as $res)
		{
			if
			(
				$res->color_name 	== $data['name_fr'] 	|| 
				$res->color_alt1 	== $data['name_alt']  	||    
				$res->ref_color 	== $data['ref']   		||   
				$res->name 			== $data['name_eng']   	||   
				$res->code 			== $data['code']
			)
			{
				$exist = TRUE;
			}
		}

		if($exist)
		{
			return ['success' => FALSE];
		}

		$self 	= new self;
		//todo : use table column name directly inside Request
		$map	= ['name_fr' => 'color_name', 'name_alt' => 'color_alt1', 'ref' => 'ref_color', 'name_eng' => 'name', 'code' => 'code']; 
		foreach($data as $k => $v)
		{
			$column 		= $map[$k];
			$self->$column 	= $v;
		}
		$self->save();

		return ['success' => TRUE, 'data' => self::find($self->id_color)];
	}

	public function edit($raw)
	{
		if(!self::find($raw['id']))
		{
			return ['success' => FALSE];
		}

		$result = self::all();
		$exist 	= FALSE;
		foreach($result as $res)
		{
			if
			(
				$res->color_name 	== $raw['name_fr'] 		|| 
				$res->color_alt1 	== $raw['name_alt']  	||    
				$res->ref_color 	== $raw['ref']   		||   
				$res->name 			== $raw['name_eng']   	||   
				$res->code 			== $raw['code']
			)
			{
					$exist = TRUE;
			}
		}

		if($exist)
		{
			return ['success' => FALSE];
		}

		$data = [];
		//todo : use table column name directly inside Request
		$map	= ['name_fr' => 'color_name', 'name_alt' => 'color_alt1', 'ref' => 'ref_color', 'name_eng' => 'name', 'code' => 'code']; 
		foreach($raw as $k => $v)
		{
			if($k != 'id')
			{
				$column 		= $map[$k];
				$data[$column] 	= $v;
			}				
		}

		self::where('id_color', '=', $raw['id'])->update($data);

		return ['success' => TRUE, 'data' => self::find($raw['id'])];
	}

	public function remove($id)
	{
		self::destroy($id);
	}
}
