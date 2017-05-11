<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model {

	//
	protected $table 		= 'apb_types';
	protected $primaryKey 	= 'id_type';

	public function getAll()
	{
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) 	[
										'id' 			=> $one->id_type, 
										'name' 			=> $one->type_name, 
										'type_alt' 		=> $one->type_alt,
										'type_display' 	=> $one->type_display,
										'type_title' 	=> $one->type_title,
										'html_name'		=> $one->html_name,
										'type_desc' 	=> $one->type_desc,
										'type_one' 		=> $one->type_one,
										'type_filter' 	=> $one->type_filter
									];
			}
		}

		return $data;
	}

	public function store($data)
	{
		$result = self::all();
		$exist = FALSE;
		foreach($result as $res)
		{
			if
			(
				$res->type_name			== $data['type_name'] 		|| 
				$res->type_alt			== $data['type_alt']  		||    
				$res->type_display		== $data['type_display']   	||   
				$res->type_title		== $data['type_title']   	||   
				$res->type_desc			== $data['type_desc'] 		||   
				$res->type_one			== $data['type_one'] 		||   
				$res->type_filter		== $data['type_filter'] 
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

		return ['success' => TRUE, 'data' => self::find($self->id_type)];
	}

	public function edit($raw)
	{
		if(!self::find($raw['id']))
		{
			return ['success' => FALSE];
		}

		$result = self::all();
		$exist = FALSE;
		foreach($result as $res)
		{
			if
			(
				$res->type_name			== $raw['type_name'] 		|| 
				$res->html_name			== $raw['html_name'] 		|| 
				$res->type_alt			== $raw['type_alt']  		||    
				$res->type_display		== $raw['type_display']   	||   
				$res->type_title		== $raw['type_title']   	||   
				$res->type_desc			== $raw['type_desc'] 		||   
				$res->type_one			== $raw['type_one'] 		||   
				$res->type_filter		== $raw['type_filter'] 
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

		self::where('id_type', '=', $raw['id'])->update($data);
		return ['success' => TRUE, 'data' => self::find($raw['id'])];
	}

	public function remove($id)
	{	
		self::destroy($id);
	}
}
