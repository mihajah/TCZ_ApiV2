<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use DB;

class Customer extends Model {

	//
	protected $table 			= 'apb_customers';
	protected $primaryKey 		= 'id_customer';
	protected $table_css		= 'apb_customer_stock_software';
	protected $table_franchise 	= 'apb_franchises';
	protected $table_status		= 'apb_customers_status';
	public static $showable     = '';

	use ModelGetProperties;

	/**
	* ws method
	*/
	public static function wsOne($id, $column = '')
	{
		//
		if($column)
		{
			self::setColumn($column);
		}

		return self::getFullSchema($id);
	}

	public static function wsAll()
	{
		//
		$all = [];
		$ids = self::getAllId();
		foreach($ids as $one)
		{
			$all[] = self::wsOne($one);
		}

		return $all;
	}

	public static function wsConnect($key)
	{
		return self::connect($key);
	}

	public static function wsAdd($verb)
	{
		$data 			= [];
		$fail	 	 	= FALSE;
		$all 	 	 	= $verb->all();
		$minimum 	 	= [
							'named',
							'enseigne',
							'adresse',
							'adresse_pc',
							'adresse_ville',
							'adresse_pays',
							'phone1',
							'firstname',
							'lastname',
							'email'
					  	  ];

		$first_infos 		= [
								'name'
					  		  ];

		foreach($minimum as $must)
		{
			if(!$verb->has($must))
			{
				$fail = TRUE;
			}
			else
			{
				if($must == 'enseigne')
				{
					$data['franchise'] = $verb->input($must); 
				}
				else
				{
					$data[$must] = $verb->input($must); 
				}	
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Those column can\'t be left as empty', 'column' => $minimum];
		}

		foreach($first_infos as $fi)
		{
			if(!$verb->has($fi))
			{
				$fail = TRUE;
			}
			else
			{
				$data[$fi] = $verb->input($fi); 				
			}
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Those column can\'t be left as empty', 'column' => $first_infos];
		}

		$data['adressef'] = $verb->input('adresse');
		if($verb->has('adressef'))
			$data['adressef'] = $verb->input('adressef');

		$data['adressef_pc'] = $verb->input('adresse_pc');
		if($verb->has('adressef_pc'))
			$data['adressef_pc'] = $verb->input('adressef_pc');

		$data['adressef_ville'] = $verb->input('adresse_ville');
		if($verb->has('adressef_ville'))
			$data['adressef_ville'] = $verb->input('adressef_ville');

		$data['adressef_pays'] = $verb->input('adresse_pays');
		if($verb->has('adressef_pays'))
			$data['adressef_pays'] = $verb->input('adressef_pays');

		$data['firstnamef'] = $verb->input('firstname');
		if($verb->has('firstnamef'))
			$data['firstnamef'] = $verb->input('firstnamef');

		$data['lastnamef'] = $verb->input('lastname');
		if($verb->has('lastnamef'))
			$data['lastnamef'] = $verb->input('lastnamef');

		$data['phone1'] = $verb->input('phone1');

		if($verb->has('phone2'))
			$data['phone2'] = $verb->input('phone2');

		if($verb->has('phone3'))
			$data['phone3'] = $verb->input('phone3');

		if($verb->has('phone4'))
			$data['phone4'] = $verb->input('phone4');

		$data['email']	= $verb->input('email');

		$inserted_id 	= self::add($data);
		$final			= self::wsOne($inserted_id);

		if($verb->has('unit_test'))
		{
			self::destroy($inserted_id);
		}

		return $final;
	}

	public static function wsEdit($verb)
	{
		//
		if(!$verb->has('id_customer'))
		{
			return ['success' => FALSE, 'error' => 'id_customer field must be provided'];
		}

		if(!self::find($verb->input('id_customer')))
		{
			return ['success' => FALSE, 'error' => 'Customer not found'];
		}

		$fail 	  = FALSE;
		$needed   = $verb->except('id_customer', 'unit_test');
		$all 	  = $verb->all();

		$editable = [
						'to_callback',
						'status',
						'newsletter',
						'notes',
						'franchise',
						'firstnamef',
						'lastnamef',
						'name',
						'stock_software',
						'first_order',
						'firstname',
						'lastname',
						'adresse',
						'adressef',
						'payment_mode',
						'adresse_ville',
						'adressef_ville',
						'phone1',
						'email',
						'adresse_pays',
						'adresse_pc',
						'adressef_pays',
						'adressef_pc',
						'named',
						'siret'
			    	];

		if(count($all) < 2)
		{
			return ['success' => FALSE, 'error' => 'At least, you must provide 2 editable field', 'possible field' => $editable];
		}

		foreach($needed as $k => $v)
		{
			if($k != 'id_customer')
			{
				if(!in_array($k, $editable))
				{
					$fail = TRUE;
				}

				if($k != 'notes')
				{
					if($verb->input($k) === '')
					{
						$fail = TRUE;
					}
				}
				
			}				
		}

		if($fail)
		{
			return ['success' => FALSE, 'error' => 'Only those column can be edited, and must filled', 'column' => $editable];
		}


		$updated = self::edit($all);

		if($updated)
		{
			return self::wsOne($all['id_customer']);
		}
	}

	/**
	* Public method
	*/
	public static function edit($raw)
	{
		//
		foreach($raw as $k => $v)
		{
			if($k != 'id_customer' && $k != 'unit_test')
				$data[$k] = $v;

			if($k == 'notes')
				$data[$k] = urlencode($v);
		}

		return self::where('id_customer', '=', $raw['id_customer'])->update($data);
	}

	public static function add($data)
	{
		$self = new self;
		foreach($data as $k => $v)
		{
			$self->$k = $v;
		}

		$self->save();
		return $self->id_customer;
	}

	public static function connect($key)
	{
		$result = ['success' => FALSE];
		$customer = self::where('key', '=', $key)->first();
		if(count($customer) > 0)
		{
			$result['success'] 	= TRUE;
			$result['customer']	= self::wsOne($customer->id_customer);
		}	

		return $result;
	} 

	public static function getAllId()
	{
		$customer = [];
		$result = self::select('id_customer')->get();
		if(count($result) == 0)
		{
			return $customer;
		}

		foreach($result as $one)
		{
			$customer[] = $one->id_customer;
		}

		return $customer;
	}

	public static function getFullSchema($id, $display = 'both')
	{
		$full = self::remapCustomerAttributes($id, 'arr');

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

	public static function setColumn($raw)
	{	
		if($raw)
		{
			$selected       = explode(',', $raw);
			self::$showable = $selected;
		}		
	}

	/**
	* Internal method
	*/
	private static function additionalRemap(Customer $db, $for)
	{
		if($for == 'phone')
		{
			$phones = [];
			for($i=1; $i<5; $i++)
			{
				$phone = 'phone'.$i;
				if($db->$phone != '')
				{
					$phones[] = $db->$phone;
				}
			}

			return $phones;
		}

		if($for == 'stock_software')
		{
			$stock = ['id' => 0, 'name' => 'No software'];
			$css = DB::table(self::getProp('table_css'))->where('id', '=', $db->stock_software)->first();
			if(count($css) > 0)
			{
				$stock['id'] 	= $css->id;
				$stock['name']	= $css->name;
			}

			return $stock;
		}		

		if($for == 'franchise')
		{
			$franchise = ['id' => 0, 'name' => 'No franchise'];
			$result = DB::table(self::getProp('table_franchise'))->where('id', '=', $db->franchise)->first();
			if(count($result) > 0)
			{
				$franchise['id'] 	= $result->id;
				$franchise['name']	= $result->name;
			}

			return $franchise;
		}

		if($for == 'status')
		{
			$status = ['id' => 0, 'name' => 'Not defined', 'color' => 'Not defined'];
			$result = DB::table(self::getProp('table_status'))->where('id', '=', $db->status)->first();
			if(count($result) > 0)
			{
				$status['id']		= $result->id;
				$status['name']		= $result->name;
				$status['color']	= $result->color;
			}

			return $status;
		}

		if($for == 'chart')
		{
			$chartData = [];
			for($i=1; $i<14; $i++)
			{
				$value = 'point'.$i;
				$chartData['point'.$i] = $db->$value;
			}

			return $chartData;
		}
	}

	protected static function remapCustomerAttributes($id, $vmode = 'obj')
	{
		$data     = [];
		$selected = self::$showable;

		if(!self::find($id))
		{
			return $data;		 
		}

		if(!is_array($selected))
		{
			$selected = [
                          'tva_number',
                          'phones',
                          'email',
                          'payment_mode',
                          'siren',
                          'siret',
                          'key',
                          'discount',
                          'stock_software',
                          'franchise',
                          'to_callback',
                          'status',
                          'alreadycalled',
                          'firstorder',
                          'lastorder',
                          'arevage_ordervalue',
                          'stats',
                          'chartData',
                          'rib',
                          'notes',
                          'newsletter',
                          'last_order_value',
                          'cart_amount',
                          'last_calldate'
			            ];
		}


		$db 						= self::find($id);
		$data['id'] 				= $db->id_customer;
		$data['name']				= $db->name;
		$data['delivery_name']		= $db->named;
		$data['address_shipping']	= [
										'firstname' => $db->firstname,
										'lastname'	=> $db->lastname,
										'street'	=> $db->adresse,
										'postcode'	=> $db->adresse_pc,
										'city'		=> $db->adresse_ville,
										'country'	=> $db->adresse_pays
									  ];
		$data['address_billing']	= [
										'firstname' => $db->firstnamef,
										'lastname'	=> $db->lastnamef,
										'street'	=> $db->adressef,
										'postcode'	=> $db->adressef_pc,
										'city'		=> $db->adresse_ville,
										'country'	=> $db->adresse_pays
									  ];

		if(in_array('tva_number', $selected))
			$data['tva_number']			= $db->tva_number;

		if(in_array('phones', $selected))
			$data['phones']				= self::additionalRemap($db, 'phone');

		if(in_array('email', $selected))
			$data['email']				= $db->email;

		if(in_array('payment_mode', $selected))
			$data['payment_mode']		= $db->payment_mode;

		if(in_array('siren', $selected))
			$data['siren']				= $db->siren;

		if(in_array('siret', $selected))
			$data['siret']				= $db->siret;

		if(in_array('key', $selected))
			$data['key']				= $db->key;

		if(in_array('discount', $selected))
			$data['discount']			= $db->discount;

		if(in_array('stock_software', $selected))
			$data['stock_software']		= self::additionalRemap($db, 'stock_software');

		if(in_array('franchise', $selected))
			$data['franchise']			= self::additionalRemap($db, 'franchise');

		if(in_array('to_callback', $selected))
			$data['to_callback']		= $db->to_callback;

		if(in_array('status', $selected))
			$data['status']				= self::additionalRemap($db, 'status');

		if(in_array('alreadycalled', $selected))
			$data['alreadycalled']		= ($db->alreadycalled) ? ['value' => $db->alreadycalled, 'display' => 'yes'] : ['value' => $db->alreadycalled, 'display' => 'no'];
		
		if(in_array('firstorder', $selected))
			$data['firstorder']			= $db->first_order;

		if(in_array('lastorder', $selected))
			$data['lastorder']			= $db->last_order;

		if(in_array('arevage_ordervalue', $selected))
			$data['arevage_ordervalue']	= $db->arevage_ordervalue;

		if(in_array('stats', $selected))
			$data['stats']				= [
										'profitability' 				=> $db->profitability,
										'profitabilityOneYear' 			=> $db->profitabilityOneYear,
										'profitabilityThreeMonth' 		=> $db->profitabilityThreeMonth,
										'turnover' 						=> $db->turnover,
										'turnoverOneYear' 				=> $db->turnoverOneYear,
										'turnoverThreeMonth' 			=> $db->turnoverThreeMonth,										
										'profitability_lifepercent' 	=> $db->profitability_lifepercent,
										'profitability_yearrpercent' 	=> $db->profitability_yearrpercent,
										'profitability_threepercent' 	=> $db->profitability_threepercent
									  ];

		if(in_array('chartData', $selected))
			$data['chartData']			= self::additionalRemap($db, 'chart');

		if(in_array('rib', $selected))
			$data['rib']				= [
										'bank' 		=> $db->rib_etablissement,
										'counter' 	=> $db->rib_guichet,
										'account'	=> $db->rib_compte,
										'key'		=> $db->rib_cle
									  ];

		if(in_array('notes', $selected))
			$data['notes']				= urldecode($db->notes);

		if(in_array('newsletter', $selected))
			$data['newsletter']			= ($db->newsletter == 1) ? ['value' => $db->newsletter, 'send' => 'yes'] : ['value' => $db->newsletter, 'send' => 'no'];
		
		if(in_array('last_order_value', $selected))
			$data['last_order_value']	= $db->last_order_value;

		if(in_array('cart_amount', $selected))
			$data['cart_amount']		= $db->cart_amount;

		if(in_array('last_calldate', $selected))
			$data['last_calldate']		= $db->last_calldate;



		if($vmode == 'obj')
			return (object) $data;

		return $data;
	}

}
