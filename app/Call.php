<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;

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
			$one[] = [
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
		$call = [];
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
		$last = [];
		$result = self::where('id_customer', '=', $id)->orderBy('date', 'desc')->first();
		if(count($result) > 0)
		{
			$last[] = 	[
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


	/**
	* Public Method
	*/
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
