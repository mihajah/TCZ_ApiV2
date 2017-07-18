<?php
namespace App\Helpers;
use DB;

class Customer
{ 

	/**
	* get Touchiz Customer par Order
	*/
	public static function getOneTczCustomerByOrder($idOrder)
	{
		$sql = "SELECT C.firstname, C.lastname, O.id_order
		FROM  `ps_customer` AS C
		LEFT JOIN  `ps_orders` AS O ON C.id_customer = O.id_customer
		WHERE O.id_order = :id_order";

		$results = DB::select($sql, ['id_order' => $idOrder]);

		if(count($results) == 0)
		{
			return [];
		}
		else
		{
			return $results[0];
		}
		
	}

}
?>