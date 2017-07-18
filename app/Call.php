<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;
use App\Customer;

class Call extends Model {

	//
	protected $table 				= 'apb_calls';
	protected $primaryKey			= 'id_call';
	protected $table_call_status	= 'apb_calls_status';

	use ModelGetProperties;

	/**
	* ws Method
	*/
	public static function wsAll()
	{
		//
		$all 	= [];
		$result = self::all();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$all[] = [
							'id' 		=> $one->id_call,
							'customer' 	=> $one->id_customer,
							'notes' 	=> urldecode($one->note),
							'status' 	=> self::getCallStatus($one->status),
							'date' 		=> $one->date,
							'flag' 		=> $one->flag
						 ];
			}
			
		}

		return $all;
	}

	public static function wsOne($id)
	{
		//
		$one 	= [];
		$result = self::find($id);
		if(count($result) > 0)
		{
			$one = [
						'id' 		=> $result->id_call,
						'customer' 	=> $result->id_customer,
						'notes' 	=> urldecode($result->note),
						'status' 	=> self::getCallStatus($result->status),
						'date' 		=> $result->date,
						'flag' 		=> $result->flag
					 ];
		}

		return $one;
	}

	public static function wsByCustomer($id)
	{
		$call   = [];
		$result = self::where('id_customer', '=', $id)->get();
		if(count($result) > 0)
		{
			foreach($result as $one)
			{
				$call[] = 	[
								'id' 		=> $one->id_call,
								'customer' 	=> $one->id_customer,
								'notes' 	=> urldecode($one->note),
								'status' 	=> self::getCallStatus($one->status),
								'date' 		=> $one->date,
								'flag' 		=> $one->flag
					 		];
			}		
		}

		return $call;
	}

	public static function wsLastCall($id)
	{
		$last   = [];
		$result = self::where('id_customer', '=', $id)->orderBy('date', 'desc')->first();
		if(count($result) > 0)
		{
			$last  = 	[
							'id' 		=> $result->id_call,
							'customer' 	=> $result->id_customer,
							'notes' 	=> urldecode($result->note),
							'status' 	=> self::getCallStatus($result->status),
							'date' 		=> $result->date,
							'flag' 		=> $result->flag
					 	];
		}

		return $last;
	}

	public static function wsAdd($verb)
	{
		$fail = FALSE;
		$all  = $verb->except('unit_test', 'status');
		$fillable = [
						'id_customer',
						'note',
						'date'
					];

		if(count($all) != count($fillable))
		{
			return ['success' => FALSE, 'error' => 'Only those column can be added', 'column' => $fillable];
		}

		foreach($fillable as $key)
		{
			if(!$verb->has($key))
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Only those column can be added', 'column' => $fillable];
		}

		if(!$verb->has('status'))
		{
			$all['status'] = 1;
		}

		$created 	= self::add($all);
		$fresh 		= self::wsOne($created);

		if($verb->has('unit_test'))
		{
			self::destroy($created);
		}

		return $fresh;
	}

	public static function wsEdit($verb)
	{
		$fail = FALSE;
		$all  = $verb->only('note', 'date');
		if(!self::find($verb->input('id')))
		{
			return ['success' => FALSE, 'error' => 'Call not found'];
		}

		$editable 	= 	[
							'note',
							'date'
						];

		$facultatif =	[
							'status',
							'flag'
						];

		foreach($editable as $key)
		{
			if(!$verb->has($key))
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'You must provide at least those field', 'field' => $editable];
		}

		foreach($facultatif as $key)
		{
			if($verb->has($key))
			{
				$all[$key] = $verb->input($key);
			}
		}

		$all['id_call'] = $verb->input('id');
		
		self::edit($all);	
		return self::wsOne($all['id_call']);
	}

	public static function wsDelete($id)
	{
		return self::destroy($id);
	}

	public static function wsCallToDo()
	{
		return self::getAllCallTodo();
	}

	/**
	* Public Method
	*/
	public static function getAllCallTodo()
	{
		$data = [];
		$sql  = "SELECT * FROM `apb_calls` as CA 
				 INNER JOIN apb_customers as CU ON CU.id_customer = CA.id_customer 
				 WHERE CA.date in (SELECT MAX(date) from apb_calls GROUP BY id_customer)";

		$result = DB::select($sql);

		if(count($result) > 0)
		{
			$data = $result;
		}

		return $data;
	}

	public static function add($data)
	{
		$call = new self;
		foreach($data as $k => $v)
		{
			if($k == 'note')
			{
				$call->$k = urlencode($v);
			}
			else
			{
				$call->$k = $v;
			}			
		}

		$call->save();
		Customer::where('id_customer', '=', $data['id_customer'])->update(['alreadycalled' => 1]);
		return $call->id_call;
	}

	public static function edit($raw)
	{
		foreach($raw as $k => $v)
		{
			if($k != 'id_call')
				$data[$k] = $v;
		}

		self::where('id_call', '=', $raw['id_call'])->update($data);
	}

	public static function getCallStatus($id)
	{
		//
		$status = ['id' => $id, 'value' => 'no assigned value'];
		$result = DB::table(self::getProp('table_call_status'))->where('id', '=', $id)->first();
		if(count($result) > 0)
		{
			$status = ['id' => $id, 'value' => $result->name];
		}

		return $status;
	}

	/**
	* Internal Method
	*/
	/*public static function()
	{
		//
	}*/

}
