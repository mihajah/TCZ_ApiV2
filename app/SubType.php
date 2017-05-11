<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SubType extends Model {

	//
	protected $table 		= 'apb_subtypes';
	protected $primaryKey 	= 'id_subtype';


	public function getAll()
	{
		$data   = [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$data[] = (object) 	[
										'id' 				=> $one->id_subtype, 
										'name' 				=> $one->subtype_name, 
										'subtype_alt' 		=> $one->subtype_alt,
										'subtype_display' 	=> $one->subtype_display,
										'html_name'			=> $one->html_name,
										'subtype_one' 		=> $one->subtype_one
									];
			}
		}

		return $data;
	}

	public function store($data)
	{
		//
		$result 		= self::all();
		$exist 			= FALSE;
		$html_name 		= str_slug($data['subtype_name']);

		foreach($result as $res)
		{
			if
			(
				$res->subtype_name		== 	$data['subtype_name'] 		|| 
				$res->subtype_alt		== 	$data['subtype_alt'] 		|| 
				$res->subtype_display	== 	$data['subtype_display']  	|| 
				$res->subtype_one		== 	$data['subtype_one']  		|| 
				$res->html_name			==	$html_name    
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

		$self->html_name = $html_name;
		$self->save();

		return ['success' => TRUE, 'data' => self::find($self->id_subtype)];
	}

	public function edit($raw)
	{
		//
		if(!self::find($raw['id']))
		{
			return ['success' => FALSE];
		}

		$result 		= self::all();
		$exist 			= FALSE;
		$html_name 		= str_slug($raw['subtype_name']);

		foreach($result as $res)
		{
			if
			(
				$res->subtype_name		== 	$raw['subtype_name'] 		|| 
				$res->subtype_alt		== 	$raw['subtype_alt'] 		|| 
				$res->subtype_display	== 	$raw['subtype_display']  	|| 
				$res->subtype_one		== 	$raw['subtype_one']  		|| 
				$res->html_name			==	$html_name    
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

		$data['html_name'] = $html_name;
		self::where('id_subtype', '=', $raw['id'])->update($data);

		return ['success' => TRUE, 'data' => self::find($raw['id'])];
	}

	public function remove($id)
	{
		self::destroy($id);
	}
}
