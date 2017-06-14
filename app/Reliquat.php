<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\Product;

class Reliquat extends Model {

	//
	protected $table    = 'apb_reliquats';
	protected $fillable = ['id_customer', 'id_order', 'id_product', 'qty_initial', 'qty_sent', 'qty_left'];

	/**
	* ws Method
	*/
	public static function wsOne($id)
	{
		//
		return self::getFullSchema($id);
	}

	public static function wsAll()
	{
		$data = [];
		$id   = parent::select('id')->get();
		
		if(count($id) == 0)
		{
			return [];
		}

		foreach($id as $one)
		{
			$data[] = self::wsOne($one->id);
		}

		return $data;
	}

	public static function wsByCustomer($id)
	{
		$data = [];
		$id   = parent::select('id')->where('id_customer', '=', $id)->get();

		if(count($id) == 0)
		{
			return [];
		}

		foreach($id as $one)
		{
			$data[] = self::wsOne($one->id);
		}

		return $data;
	}

	public static function wsByOrder($id)
	{
		$data = [];
		$id   = parent::select('id')->where('id_order', '=', $id)->get();

		if(count($id) == 0)
		{
			return [];
		}

		foreach($id as $one)
		{
			$data[] = self::wsOne($one->id);
		}

		return $data;
	}

	/**
	* public method
	*/
	public static function getFullSchema($id, $display = 'both')
	{
		$full = self::remapReliquatAttributes($id, 'arr');

		if($display == 'both')
		{
			return $full;
		}

		if($display == 'key')
		{
			$key = [];
			foreach($full as $k => $v)
			{
				$key[] = $k;
			}
			
			return $key;
		}

		if($display == 'value')
		{
			$value = [];
			foreach($full as $k => $v)
			{
				$value[] = $v;
			}

			return $value;
		}
	}

	public static function wsAdd($data)
	{
		$created = parent::create($data);

		if($created->id)
		{
			return ['success' => TRUE, 'data' => self::wsOne($created->id)];
		}
		else
		{
			return ['success' => FALSE, 'error' => 'fail saving ...'];
		}
	}

	public static function beforeSave($raw)
	{
		$n = parent::where('id_product', '=', $raw['id_product'])->get()->count();
		if($n > 0)
		{
			foreach($raw as $k => $v)
			{
				if($k != 'id_product')
					$data[$k] = $v;
			}

			$updated = parent::where('id_product', '=', $raw['id_product'])->update($data);
			
			if($updated)
			{
				$id = parent::where('id_product', '=', $raw['id_product'])->first()->id;
				return ['success' => TRUE, 'data' => $id];
			}				
			else
			{
				return ['success' => FALSE];
			}
				
		}
		else
		{
			return ['success' => FALSE];
		}		
	}


	/**
	* internal method
	*/
	protected static function remapReliquatAttributes($id, $vmode = 'obj')
	{
		if(!parent::find($id))
		{
			return [];
		}

		$data     = [];
		$r        = parent::find($id);
		$customer = Customer::find($r->id_customer);
		$product  = Product::find($r->id_product);


		$data['id']       = $r->id;
		$data['customer'] = ($customer) ? ['id' => $customer->id_customer, 'name' => $customer->name] : [];
		$data['order']    = ['id' => $r->id_order];
		$data['product']  = ($product) ? ['id' => $r->id_product, 'name' => Product::wsOne($r->id_product, 'obj')->name] : [];
		$data['quantity'] = ['initial' => $r->qty_initial, 'sent' => $r->qty_sent, 'left' => $r->qty_left];
		$data['date']     = ['created' => substr($r->created_at, 0, 19), 'updated' => substr($r->updated_at, 0, 19)];

		if($vmode == 'obj')
			return (object) $data;

		return $data;
	}

}
