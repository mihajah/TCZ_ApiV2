<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model {

	//
	protected $table 		= 'apb_features';
	protected $primaryKey 	= 'id_feature';

	public function getAll()
	{
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) 	[
										'id' 			=> $one->id_feature, 
										'name' 			=> $one->feature_name, 
										'display_name' 	=> $one->display_name,
										'filter' 		=> $one->filter,
										'menu' 			=> $one->menu
									];
			}
		}

		return $data;
	}

	public function store($data)
	{
		$result = self::all();
		$exist  = FALSE;
		foreach($result as $res)
		{
			if
			(
				$res->feature_name	== $data['feature_name'] 	|| 
				$res->display_name	== $data['display_name']  
			)
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

		return ['success' => TRUE, 'data' => self::find($self->id_feature)];
	}

	public function edit($raw)
	{
		if(!self::find($raw['id']))
		{
			return ['success' => FALSE];
		}

		$result = self::all();
		$exist  = FALSE;
		foreach($result as $res)
		{
			if
			(
				$res->feature_name	== $raw['feature_name'] 	|| 
				$res->display_name	== $raw['display_name']  
			)
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

		self::where('id_feature', '=', $raw['id'])->update($data);

		return ['success' => TRUE, 'data' => self::find($raw['id'])];
	}

	public function remove($id)
	{
		self::destroy($id);
	}
}
