<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use App\Order;
use App\Product;

class Stock extends Model {

	protected $table 	  		 = 'ps_stock_available';
	protected $table_fordevice	 = 'apb_prd_fordevice';
	protected $primaryKey 		 = 'id_stock_available';
	protected static $product_pk = 'id_product';
	protected static $local_k	 = 'id_product';


	use ModelGetProperties;

	public static function get($product, $display = '')
	{
		$bdd      = self::getAvailable($product);
		$current  = Order::getCurrentStock($product);
		$real 	  = $bdd + $current;
		$stock = ['available' => $bdd, 'real' => $real];

		if($display)
		{
			return $stock[$display];
		}

		return $stock; 
	}

	public static function set($product, $quantity)
	{
		return self::where('id_product', '=', $product)->update(['quantity' => $quantity]);
	}

	public static function fromBox($box)
	{
		$qty = 0;
		$many = Product::where('numbox', '=', $box)->get();
		if(count($many) > 0)
		{
			foreach($many as $p)
			{
				$qty += Stock::where('id_product', '=', $p->id_product)->first()->quantity;
			}
		}

		return $qty;
	}

	protected static function getAvailable($product)
	{
		$stock = 0;
		$results = self::where('id_product', '=', $product)->get();
		if($results->count() > 0)
		{
			foreach($results as $q)
			{
				$stock += $q->quantity;
			}
		}			

		return $stock;
	}

	public static function linkForDevice()
	{
		$table_one = self::getProp('table');
		$table_two = self::getProp('table_fordevice');
		$link = self::join($table_two, $table_one.'.id_product', '=', $table_two.'.id_product');
		$data = ['link' => $link, 't1' => $table_one, 't2' => $table_two];
		return $data;
	}

}


