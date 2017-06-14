<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;
use Mail;
use App\Customer;
use App\Product;
use App\Stock;
use App\Helpers\GazFactory as GF;

class Order extends Model {

	//
	protected $table 				= 'apb_reseller_orders';
	protected $primaryKey 			= 'id_reseller_order';
	protected $order_apb_table 		= 'apb_reseller_carts';
	protected $order_ps_table 		= 'ps_orders';
	protected $order_ps_cart_table 	= 'ps_cart_product';
	protected $customer_table		= 'apb_customers';
	protected $table_tmp_cart		= 'apb_reseller_tmp_carts';
	public static $showable         = '';

	use ModelGetProperties;

	/**
	* ws Method
	*/
	public static function wsOne($id, $column = '')
	{
		//	
		if($column)
		{
			self::setColumn($column);
		}

		return self::getFullSchema($id, FALSE);
	}

	public static function wsAll()
	{
		$id 	= [];
		$result = self::getAllId(FALSE);
		if(count($result) == 0)
		{
			return $id;
		}

		foreach($result as $one)
		{
			$id[] = self::getFullSchema($one->id_reseller_order, FALSE);
		}

		return $id;
	}

	public static function wsForCustomer($id)
	{
		$fc 	= [];
		$order 	= self::getForCustomerId($id, FALSE);
		if(count($order) == 0)
		{
			return $fc;
		}

		foreach($order as $one)
		{
			$fc[] = self::getFullSchema($one, FALSE);
		}

		return $fc;
	}

	public static function wsShowCart($id)
	{
		return self::getTmpCart($id, FALSE);
	}

	public static function wsToShip()
	{
		$toShip = [];
		$id 	= self::getAllId();
		if(count($id) == 0)
		{
			return [];
		}

		foreach($id as $one)
		{
			$newOrder = self::getFullSchema($one->id_reseller_order, FALSE);
			if($newOrder['status'] == 2)
			{
				$toShip[] = $newOrder;
			}
		}

		return $toShip;
	}

	public static function wsWithEan($id)
	{
		$order = self::getFullSchema($id, FALSE);

		if(!isset($order['cart']))
		{
			return ['success' => FALSE, 'error' => 'Cart not found'];
		}

		if(count($order['cart']) == 0)
		{
			return ['success' => FALSE];
		}

		$str 			= "label front;label back;ean\r\n";
		$product_list 	= [];

		foreach($order['cart'] as $cart)
		{
			foreach($cart as $k => $v)
			{
				$product 		= Product::wsOne($k, 'obj');
				$product_list[] = ['id' => $product];
				for($i=0; $i<$v; $i++)
				{
					if(isset($product->fordevice[0]))
					{
						$fd = $product->fordevice[0]['name'];
					}
					else
					{
						$fd = '';
					}

					$str .= $product->forbrand[0]['name']." ".$fd." ".$product->subtype['name'];
					$str .= " ".$fd." ".$product->color['name']." (".$product->box.");";
					$str .= $product->ean."\r\n";
				}
			}
		}

		return ['succes' => TRUE, 'file' => $str, 'p' => $product_list];
	}

	public static function wsAdd($verb, $staging = FALSE)
	{
		if(!$verb->has('id_customer'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_customer'];
		}

		$id_customer = $verb->input('id_customer');
		$fresh = self::newROFC($id_customer);	
		if($verb->has('unit_test'))
		{
			self::destroy($fresh['id']);
		}	

		return $fresh;
	}

	public static function wsAddCart($verb, $staging = FALSE)
	{
		$all 		= $verb->all();
		$fail 		= FALSE;
		$fillable 	= 	[
							'id_order', 
							'id_product', 
							'quantity'
						];

		foreach($fillable as $key)
		{
			if(!$verb->has($key))
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Those field must be provided', 'field' => $fillable];
		}

		if($all['quantity'] < 0)
		{
			return ['success' => FALSE, 'error' => 'Quantity must > 0'];
		}

		if(!self::find($all['id_order']))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(self::find($all['id_order'])->status != 1)
		{
			return ['success' => FALSE, 'error' => 'This request only works with order with status = 1 ( order creation step )'];
		}

		$missing = self::addToCart($all['id_order'], $all['id_product'], $all['quantity']);
		DB::table(self::getProp('table').($staging?"_staging":""))
		->where('id_reseller_order', '=', $all['id_order'])
		->update(['lastupdate_date' => @date('Y-m-d H:i:s')]);

		if($missing)
		{
			if($missing == $all['quantity'])
			{
				return ['success' => FALSE, 'error' => '0 product available'];
			}
		}

		return ['success' => TRUE, 'missing' => $missing];
	}

	public static function wsAddCartSubmit($verb, $staging = FALSE)
	{
		//
		$all 		= $verb->all();
		$fail 		= FALSE;
		$fillable 	= 	[
							'id_order',
							'cart'
						];

		foreach($fillable as $key)
		{
			if(!$verb->has($key))
			{
				$fail = TRUE;
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Those field must be provided', 'field' => $fillable];
		}

		if(count($all['cart']) == 0)
		{
			return ['success' => FALSE, 'error' => 'Cart is empty'];
		}

		if(!self::find($all['id_order']))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(self::find($all['id_order'])->status != 1)
		{
			return ['success' => FALSE, 'error' => 'This request only works with order with status = 1 ( order creation step )'];
		}

		//set delivery24, and shipping_fee
		$data['delivery24'] 	= $verb->input('delivery24');
		$data['shipping_fee'] 	= $verb->input('shipping_fee');
		DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $all['id_order'])->update($data);

		$missing = self::submitCart($all['cart'], $all['id_order']);
		if($missing['totalAdded'] == 0)
		{
			return ['success' => FALSE, 'error' => 'no product available'];
		}

		self::goToNextStep($all['id_order']);
		unset($missing['totalAdded']);

		//notif
			$content['subject'] 	= "Nouvelle commande module revendeurs (".$all['id_order'].")";
			$content['content'] 	= "Une commande vient d'être passée. Le numéro de commande est <b>".$all['id_order']."</b>";

			if(!$verb->has('unit_test'))
			{
				/*Mail::send(['html' => 'emails.orderSubmit'], ['mail_content' => $content], function($message) use ($content)
				{
					$message->from('info-techtablet@techtablet.fr', 'Techtablet');
				    //$message->to('xanaviarta@gmail.com', 'Mihaja')->subject($content['subject']); //debug
				    $message->to('steve.queroub@gmail.com', 'Steve')->subject($content['subject']);
				    $message->to('anne-sophie@techtablet.fr', 'Sophie')->subject($content['subject']);		    
				});*/
			}			
		//--

		if($verb->has('unit_test'))
			self::wsAddRollBack($verb);


		return ['success' => TRUE, 'missing' => $missing];
	}

	public static function wsAddDelivery($verb, $staging = FALSE)
	{
		//
		if(!$verb->has('id_order'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_order'];
		}

		$order = $verb->input('id_order');
		if(!self::find($order))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(self::find($order)->status != 1)
		{
			return ['success' => FALSE, 'error' => 'This request only works with order with status = 1 ( order creation step )'];
		}	

		if(!$verb->has('shipping_fee') || !$verb->has('delivery24'))
		{
			return ['success' => FALSE, 'error' => 'You must provide shipping_fee and delivery24'];
		}

		$data['shipping_fee'] 	= $verb->input('shipping_fee');
		$data['delivery24'] 	= $verb->input('delivery24');

		if($verb->has('unit_test'))
		{
			if(!self::find($order))
			{
				return ['success' => FALSE, 'error' => 'Order not found'];
			}
		}
		else
		{
			DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $order)->update($data);
		}

		return self::wsOne($order);
	}

	public static function wsAddValidate($verb)
	{
		if(!$verb->has('id_order'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_order'];
		}

		if(!$verb->has('cart'))
		{
			return ['success' => FALSE, 'error' => 'You must provide cart'];
		}

		if(count($verb->input('cart')) == 0)
		{
			return ['success' => FALSE, 'error' => 'Cart is empty'];
		}

		$order 	= $verb->input('id_order');
		$cart 	= $verb->input('cart');

		if(!self::find($order))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(self::find($order)->status != 1)
		{
			return ['success' => FALSE, 'error' => 'This request only works with order with status = 1 ( order creation step )'];
		}

		$missing = self::validateCart($cart);
		
		if($missing['success'] == FALSE)
		{
			unset($missing['success']);
			return ['success' => FALSE, 'missing' => $missing];
		}

		unset($missing['success']);
		return ['success' => TRUE, 'missing' => $missing];
	}

	public static function wsAddShipped($verb, $staging = FALSE)
	{
		//
		if(!$verb->has('id_order'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_order'];
		}

		$order 	= $verb->input('id_order');
		if(!self::find($order))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(self::find($order)->status != 2)
		{
			return ['success' => FALSE, 'error' => 'This request only works with order with status = 2 ( order preparation step )'];
		}	

		if(!$verb->has('discount'))
		{
			return ['success' => FALSE, 'error' => 'You must provide discount'];
		}

		$discount = floatval($verb->input('discount'));
		if($discount > 0.0)
		{
			$data['discount'] = $discount;
			DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $order)->update($data);
		}

		if($verb->has('shipping_number') && $verb->input('shipping_number') != '')
		{
			DB::table(self::getProp('table'))->where('id_reseller_order', '=', $order)->update(['shipping_number' => $verb->input('shipping_number')]);
		}

		self::goToNextStep($order);
		//notif
			$content['subject'] 	= "Commande Techtablet ".$order." expédiée";
			$customer 				= Customer::find(self::find($order)->id_customer);
			$content['name']		= $customer->firstname." ".$customer->lastname;
			$content['mail']		= $customer->email;
			$content['reference']	= $order;
			$content['lien']		= 'http://www.techtablet.fr/ordermodule/Customer.html?key='.$customer->key;
			$content['num_suivi']	= '';

			if(!$verb->has('unit_test'))
			{
				/*Mail::send([], [], function($message) use ($content)
				{
					$message->from('info-techtablet@techtablet.fr', 'Techtablet');
				   // $message->to('xanaviarta@gmail.com', 'Mihaja')->subject($content['subject'])
				   // ->setBody(GF::mailShipping($content['name'], $content['reference'], $content['lien'], $content['num_suivi']), 'text/html'); //debug
				    $message->to($content['mail'], $content['name'])->subject($content['subject'])
				    ->setBody(GF::mailShippingHtmlBody($content['name'], $content['reference'], $content['lien'], $content['num_suivi']), 'text/html');		    
				});*/
			}			
		//---

		if($verb->has('unit_test'))
		{
			$o = self::find($order);
			$o->status--;
			$data['status'] = $o->status;
			DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $order)->update($data);
		}

		return ['success' => TRUE];
	}

	public static function wsAddPaid($verb, $staging = FALSE)
	{
		if(!$verb->has('id_order'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_order'];
		}

		$order 	= $verb->input('id_order');
		if(!self::find($order))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(self::find($order)->status != 3)
		{
			return ['success' => FALSE, 'error' => 'This request only works with order with status = 3 ( order sent step )'];
		}

		self::goToNextStep($order);

		if($verb->has('unit_test'))
		{
			$o = self::find($order);
			$o->status--;
			$data['status'] = $o->status;
			DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $order)->update($data);
		}

		return ['success' => TRUE];
	}

	public static function wsAddRollBack($verb)
	{
		if(!$verb->has('id_order'))
		{
			return ['success' => FALSE, 'error' => 'You must provide id_order'];
		}

		$order 	= $verb->input('id_order');
		if(!self::find($order))
		{
			return ['success' => FALSE, 'error' => 'Order not found'];
		}

		if(self::find($order)->status != 2)
		{
			return ['success' => FALSE, 'error' => 'This request only works with order with status = 2 ( order sent step )'];
		}

		$oldCart = self::goToPreviousStep($order);
		return ['success' => TRUE, 'cart' => $oldCart];
	}

	/**
	* Public Method
	*/
	public static function validateCart($cart)
	{
		if(!is_array($cart))
		{
			return ['success' => FALSE, 'error' => 'Cart must be an array'];
		}

		$missing 			= [];
		$missing['success'] = TRUE;
		$totalAdded 		= 0;
		foreach($cart as $k => $v)
		{
			$nAdded      = self::validateProductInCart(intval($k), $v);
			$totalAdded += $nAdded;

			if($nAdded < $v)
			{
				$missing[$k] 		= $v - $nAdded;
				$missing['success'] = FALSE;
			} 
		}

		return $missing;
	}

	public static function validateProductInCart($p, $q)
	{
		$availableQty = Stock::get($p, 'available');
		if($q > $availableQty)
		{
			$q = $availableQty;
		}

		return $q;
	}

	public static function goToNextStep($id, $staging = FALSE)
	{
		$o = self::find($id);
		if($o->status < 4)
		{
			$o->status++;
			$data['status'] = $o->status;
			DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $id)->update($data);

			if($o->status == 2 || ($o->status == 3 && $o->billing_number == 0))
			{
				self::orderAssignId($id);
			}
		}
	}

	public static function goToPreviousStep($id, $staging = FALSE)
	{
		$oldCart = [];
		$status  = self::find($id)->status;
		if($status == 2)
		{
			$oldCart = self::resetCart($id, $staging);
		}	

		if($status > 1)
		{
			$status--;
			$data['status'] = $status;
			DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $id)->update($data);
		}

		return $oldCart;
	}

	public static function orderAssignId($id, $staging = FALSE)
	{
		$order = DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $id)->first();
		if(count($order))
		{
			if($order->unique_id == 0)
			{
				$n = DB::table(self::getProp('table').($staging?"_staging":""))->where('status', '>', 1)->count();
				$nRef = $n + 9;

				$data['unique_id'] 		= $nRef;
				$data['billing_date'] 	= @date('Y-m-d H:i:s');
				DB::table(self::getProp('table').($staging?"_staging":""))->where('id_reseller_order', '=', $id)
				->where('id_reseller_order', '=', $id)
				->update($data);
			}
		}
	}

	public static function resetCart($id, $staging = FALSE)
	{
		$oldCart = [];
		$result  = DB::table(self::getProp('order_apb_table').($staging?"_staging":""))->where('id_reseller_order', '=', $id)->get();
		if(count($result) == 0)
		{
			return $oldCart;
		}	

		foreach($result as $one)
		{
			$product = $one->id_product;
			if(!$staging)
			{
				$qty 				= $one->quantity;
				$oldCart[$product] 	= $qty;
				$oldQty 			= Stock::get($product, 'available');
				$newQty 			= $oldQty + $qty;

				Stock::set($product, $newQty);
			}
		}

		DB::table(self::getProp('order_apb_table').($staging?"_staging":""))->where('id_reseller_order', '=', $id)->delete();
		return $oldCart;
	}

	//$cart = ['id_product' => 'quantity'];
	public static function submitCart($cart, $id)
	{
		self::resetCart($id);
		$totalAdded = 0;
		$missing 	= [];
		foreach($cart as $k => $v)
		{
			$added = self::addToCart($id, intval($k), $v);
			$totalAdded += $added;
			if($added < $v)
			{
				$missing[$k] = $v - $added;
			}
		}

		$missing['totalAdded'] = $totalAdded;
		return $missing;
	}

	public static function addToCart($order, $product, $qty, $staging =  FALSE)
	{
		$available = Stock::get($product, 'available');
		if($qty > $available)
		{
			$qty = $available;
		}

		if(!$staging)
		{
			$new = $available - $qty;
			Stock::set($product, $new);
		}

		$data['id_reseller_order'] 	= $order;
		$data['id_product'] 		= $product;
		$data['quantity']			= $qty;

		DB::table(self::getProp('order_apb_table').($staging?"_staging":""))->insert($data);
		return $qty;
	}

	//ROFC = Reseller Order For Customer
	public static function newROFC($id, $staging = FALSE)
	{
		$sql = "SELECT O.id_reseller_order 
				FROM ".self::getProp('table').($staging?"_staging":"")." as O 
				WHERE O.status = 1 AND O.id_customer = ".$id;
		$result = DB::select($sql);
		if(count($result) == 0)
		{
			return ['success' => FALSE, 'error' => 'not found with status 1'];
		}

		if(!Customer::find($id))
		{
			return ['success' => FALSE, 'error' => 'Customer: '.$id.' not found'];
		}

		$c 						= Customer::find($id);
		$data['id_customer'] 	= $id;
		$data['status'] 		= 1;
		$data['discount'] 		= 0.0;
		$data['unique_id'] 		= 0;
		$data['payment_method'] = $c->payment_mode;

		$last = DB::table(self::getProp('table').($staging?"_staging":""))->insertGetId($data);
		return self::wsOne($last);
	}

	public static function getTmpCart($order, $staging)
	{
		$tmp = [];
		$sql = "SELECT id_product,quantity 
				FROM ".self::getProp('table_tmp_cart').($staging?"_staging":"")." 
				WHERE `id_reseller_order`=".$order;

		$result = DB::select($sql);
		if(count($result) == 0)
		{
			return [];
		}

		foreach($result as $one)
		{
			$tmp[] = ['id_product' => $one->id_product, 'quantity' => $one->quantity];
		}

		return $tmp;
	}

	public static function getForCustomerId($id, $staging)
	{
		$order = [];
		$sql = "SELECT O.id_reseller_order 
				FROM ".self::getProp('table').($staging?"_staging":"")." 
				as O WHERE id_customer=".$id." 
				ORDER BY O.id_reseller_order DESC";

		$result = DB::select($sql);
		if(count($result) == 0)
		{
			return $order;
		}

		foreach($result as $one)
		{
			$order[] = $one->id_reseller_order;
		}
		
		return $order;
	}

	public static function getAllId($staging = FALSE)
	{
		if($staging)
		{
			$result = DB::table(self::getProp('table_staging'))->orderBy('id_reseller_order', 'desc')->get();
		}
		else
		{
			$result = self::all()->sortByDesc('id_reseller_order');
		}

		return $result;
	}

	/**
	* obsolete fx
	* need to update '->where($po.'.current_state', '<', 4)'
	* better use Stock::get($product, 'real') instead
	*/
	public static function getCurrentStock($id_product)
	{
		$ro   = self::getProp('table');
		$rc   = self::getProp('order_apb_table');
		$po   = self::getProp('order_ps_table');
		$pcp  = self::getProp('order_ps_cart_table');
		$real = 0;

		$link = self::join($rc, $ro.'.id_reseller_order', '=', $rc.'.id_reseller_order')
		->select($rc.'.quantity AS QT')
		->where($rc.'.id_product', '=', $id_product)
		->where($ro.'.status', '<', 3)
		->get();

		if(count($link) > 0)
		{
			foreach($link as $row)
			{
				$real += $row->QT;
			}
		}

		$link = DB::table($po)->join($pcp, $po.'.id_cart', '=', $pcp.'.id_cart')
		->select($pcp.'.quantity AS QT')
		->where($pcp.'.id_product', '=', $id_product)
		->where($po.'.current_state', '<', 4)
		->get();

		if(count($link) > 0)
		{
			foreach($link as $row)
			{
				$real += $row->QT;
			}
		}

		return $real;
	}

	public static function getSold($id, $time = '', $type = [])
	{
		$pcp  = self::getProp('order_ps_cart_table');
		$po   = self::getProp('order_ps_table');
		$rc   = self::getProp('order_apb_table');
		$ro   = self::getProp('table');

		if(!empty($type))
		{
			if($type['side'] == 'touchiz')
			{
				$date = $type['date'];
				if(!$date)
				{
					$sql = "SELECT SUM( C.quantity ) AS totalNumber
							FROM ".$pcp." AS C
							LEFT JOIN ".$po." AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state = 4 OR O.current_state = 5 )";
				}
				else
				{
					$sql = "SELECT SUM( C.quantity ) AS totalNumber
							FROM ".$pcp." AS C
							LEFT JOIN ".$po." AS O ON O.id_cart = C.id_cart
							WHERE C.id_product = '".$id."'
							AND ( O.current_state = 4 OR O.current_state = 5 ) AND O.date_add>'".$date."'";
				}

				$results = DB::select($sql);	
				if(count($results) > 0)
				{
					return ''.$results[0]->totalNumber;
				}
				else
				{
					return 0;
				}	
			}

			if($type['side'] === 'techtablet')
			{
				$date = $type['date'];
				if(!$date)
				{
					$sql = "SELECT SUM(C.quantity) AS totalNumber 
							FROM ".$rc." AS C
							INNER JOIN ".$ro." AS O ON O.id_reseller_order = C.id_reseller_order
							WHERE C.id_product='".$id."' AND O.status > 3";
				}
				else
				{
					$sql = "SELECT SUM(C.quantity) AS totalNumber 
							FROM ".$rc." AS C
							INner JOIN ".$ro." AS O ON O.id_reseller_order = C.id_reseller_order
							WHERE C.id_product='".$id."' AND O.status > 3  AND O.billing_date>'".$date."'";
				}

				$results = DB::select($sql);	
				if(count($results) > 0)
				{
					return ''.$results[0]->totalNumber;
				}
				else
				{
					return 0;
				}
			}
		}
		else
		{
			$sql = 'SELECT '.$pcp.'.id_product, '.$pcp.'.quantity
					FROM '.$pcp.' 
					INNER JOIN '.$po.' ON '.$po.'.id_cart = '.$pcp.'.id_cart 
					AND '.$po.'.valid = 1
					WHERE '.$pcp.'.id_product = '.$id.' 
					AND ('.$po.'.date_add BETWEEN DATE_SUB(NOW(), INTERVAL '.$time.' DAY) 
					AND NOW())';
			$results = DB::select($sql);

			$qty = 0;
			if(count($results > 0))
			{
				foreach($results as $q)
				{
					$qty += $q->quantity;
				}
			}

			return ['value' => $qty, 'display' => $qty];
		}			
	}

	public static function getFullSchema($id, $staging, $display = 'both')
	{
		$full = self::remapOrderAttributes($id, $staging, 'arr');

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

	public static function getCart($id, $staging = FALSE)
	{
		$cart   = [];
		$sql    = "SELECT * FROM ".self::getProp('order_apb_table').($staging?"_staging":"")." WHERE `id_reseller_order`= ".$id;
		$result = DB::select($sql);
		
		if(count($result) == 0)
		{
			return $cart;
		}

		foreach($result as $c)
		{
			$cart['format_one'][] = [$c->id_product => $c->quantity];
			$cart['format_two'][] = ['produit' => $c->id_product, 'qty' => $c->quantity];
		}

		return $cart;
	}


	public static function setColumn($raw)
	{
		if($raw)
		{
			$selected       = explode(',', $raw);
			self::$showable = $selected;
		}
	}

	/**
	* Internal Method
	*/
	protected static function remapOrderAttributes($id, $staging = FALSE, $vmode = 'obj')
	{
		$order    = [];
		$selected = self::$showable;

		$sql = "SELECT O.*,C.name AS customer_name FROM ".self::getProp('table').($staging?"_staging":"")." as O
				LEFT JOIN ".self::getProp('customer_table')." AS C ON C.id_customer = O.id_customer  
				WHERE id_reseller_order = ".$id;

		$result = DB::select($sql);

		if(count($result) == 0)
		{
			return $order;
		}

		if(!is_array($selected))
		{
			$selected = [
				           'fake',
				           'cart',
				           'cart2',
				           'transaction',
				           'transaction_date',
				           'shipping_number',
				           'shipping_mode',
				           'chronopost',
				           'total_cart'
			            ];
		}

		$row = $result[0];

		$order['id'] 				= $row->id_reseller_order;
		$order['status'] 			= $row->status;
		$order['customer']			= ['id' => $row->id_customer, 'name' => $row->customer_name];
		$order['discount']			= $row->discount;
		$order['billing_number']	= $row->unique_id;
		$order['billing_date']		= $row->billing_date;
		$order['shipping_fee']		= $row->shipping_fee;
		$order['delivery24']		= $row->delivery24;
		$order['payment_method']	= $row->payment_method;
		$order['lastupdate_date']	= $row->lastupdate_date;

		if(in_array('fake', $selected))
			$order['fake']				= $staging;

		if(in_array('cart', $selected))
		{
			$cart 						= self::getCart($row->id_reseller_order);
			$order['cart']				= (isset($cart['format_one'])) ? $cart['format_one'] : $cart;	
		}			
		
		if(in_array('cart2', $selected))
		{
			if(!isset($order['cart']))
			{
				$cart = self::getCart($row->id_reseller_order);
			}

			$order['cart2']				= (isset($cart['format_two'])) ? $cart['format_two'] : $cart;
		}
			
		if(in_array('transaction', $selected))
			$order['transaction']		= 0;

		if(in_array('transaction_date', $selected))
			$order['transaction_date']	= '';

		if(in_array('shipping_number', $selected))
			$order['shipping_number']	= $row->shipping_number;

		if(in_array('shipping_mode', $selected))
			$order['shipping_mode']		= $row->shipping_mode;

		if(in_array('chronopost', $selected))
			$order['chronopost']        = ['package' => ['width' => $row->longueur, 'height' => $row->largeur, 'depth' => $row->hauteur, 'weight' => $row->poids]];
		
		if(in_array('total_cart', $selected))
			$order['total_cart']        = $row->total_cart;


		if($vmode == 'obj')
			return (object) $order;

		return $order;
	}
}
