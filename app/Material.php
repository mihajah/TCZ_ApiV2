<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model {

	//
	protected $table 		= 'apb_materials';
	protected $primaryKey 	= 'id_material';

	public function getAll()
	{
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) 	[
										'id' 			=> $one->id_material, 
										'name' 			=> $one->material_name, 
										'supplier_name' => $one->supplier_name,
										'menu' 			=> $one->menu
									];
			}
		}

		return $data;
	}

	public function store($raw)
	{
		$result = self::all();
		$exist 	= FALSE;
		foreach($result as $res)
		{
			if($res->material_name == $raw['material_name'])
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

		$self = new self;
		foreach($raw as $k => $v)
		{
			$self->$k = $v;
		}

		$self->save();

		return ['success' => TRUE, 'data' => self::find($self->id_material)];
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
			if($res->material_name == $raw['material_name'])
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

		self::where('id_material', '=', $raw['id'])->update($data);

		return ['success' => TRUE, 'data' => self::find($raw['id'])];
	}

	public function remove($id)
	{
		self::destroy($id);
	}
}
