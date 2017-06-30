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

	public function store($data)
	{
		$result = self::all();
		$exist 	= FALSE;

		foreach($result as $res)
		{
			if($res->pattern_name == $data['pattern_name'])
			{
					$exist = TRUE;
			}
				
			if($data['supplier_name'] != "" && $res->supplier_name == $data['supplier_name'])
			{
					$exist = TRUE;
			}
		}

		if($exist)
		{
			return ['success' => FALSE];
		}

		$self = new self;
		foreach($data as $k => $v)
		{
			$self->$k = $v;
		}

		$self->save();

		return ['success' => TRUE, 'data' => self::find($self->id_pattern)];
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
			if($res->pattern_name == $raw['pattern_name'])
			{
					$exist = TRUE;
			}
				
			if($raw['supplier_name'] != "" && $res->supplier_name == $raw['supplier_name'])
			{
					$exist = TRUE;
			}
		}

		if($exist)
		{
			return ['success' => FALSE];
		}

		foreach($raw as $k => $v)
		{
			if($k != 'id')
				$data[$k] = $v;
		}
		
		self::where('id_pattern', '=', $raw['id'])->update($data);

		return ['success' => TRUE, 'data' => self::find($raw['id'])];
	}

	public function remove($id)
	{
		self::destroy($id);
	}
}
