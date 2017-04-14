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

	use ModelGetProperties;

	/**
	* ws method
	*/
	public static function wsOne($id)
	{
		//
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

	/**
	* Public method
	*/
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
		$data = [];
		if(!self::find($id))
		{
			return $data;		 
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
		$data['tva_number']			= $db->tva_number;
		$data['phones']				= self::additionalRemap($db, 'phone');
		$data['email']				= $db->email;
		$data['payment_mode']		= $db->payment_mode;
		$data['siren']				= $db->siren;
		$data['siret']				= $db->siret;
		$data['key']				= $db->key;
		$data['discount']			= $db->discount;
		$data['stock_software']		= self::additionalRemap($db, 'stock_software');
		$data['franchise']			= self::additionalRemap($db, 'franchise');
		$data['to_callback']		= $db->to_callback;
		$data['status']				= self::additionalRemap($db, 'status');
		$data['alreadycalled']		= ($db->alreadycalled) ? ['value' => $db->alreadycalled, 'display' => 'yes'] : ['value' => $db->alreadycalled, 'display' => 'no'];
		$data['firstorder']			= $db->first_order;
		$data['lastorder']			= $db->last_order;
		$data['arevage_ordervalue']	= $db->arevage_ordervalue;
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
		$data['chartData']			= self::additionalRemap($db, 'chart');
		$data['rib']				= [
										'bank' 		=> $db->rib_etablissement,
										'counter' 	=> $db->rib_guichet,
										'account'	=> $db->rib_compte,
										'key'		=> $db->rib_cle
									  ];
		$data['note']				= urldecode($db->notes);
		$data['newsletter']			= ($db->newsletter == 1) ? ['value' => $db->newsletter, 'send' => 'yes'] : ['value' => $db->newsletter, 'send' => 'no'];
		
		if($vmode == 'obj')
			return (object) $data;

		return $data;
	}

}
