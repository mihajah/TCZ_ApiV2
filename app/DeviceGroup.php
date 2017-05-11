<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceGroup extends Model {

	//
	protected $table 		= 'apb_devices_groups';
	protected $primaryKey 	= 'id_group';

	public function getAll()
	{
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) ['id' => $one->id_group, 'name' => $one->group_name, 'forBrand' => $one->id_brand];
			}
		}

		return $data;
	}

	public function getPopByFilter($name, $brand)
	{
		$data = '';
		$result = self::where('group_name', '=', $name)->where('id_brand', '=', $brand)->first();
		if(count($result) > 0)
		{
			$data = $result->group_popularity;
		}		

		return $data;
	}

	public function store($data)
	{
		$self = new self;
		foreach($data as $k => $v)
		{
			$self->$k = $v;
		}
		$self->save();

		return self::find($self->id_group);
	}

	public function edit($raw)
	{
		$data = [];
		foreach($raw as $k => $v)
		{
			if($k != 'id_group')
			{
				$data[$k] = $v;
			}
		}

		self::where('id_group', '=', $raw['id_group'])->update($data);
		return self::find($raw['id_group']);
	}

	public function getAllByBrand($brand)
	{	
		$data 	= [];
		$result = self::where('id_brand', '=', $brand)->get();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) ['id' => $one->id_group, 'name' => $one->group_name, 'forBrand' => $one->id_brand];
			}
		}

		return $result;
	}

	public function remove($id)
	{
		self::destroy($id);
	}

}
