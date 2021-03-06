<?php

class ProductTest extends TestCase {

	protected $isTestable = TRUE;

	/**
	* All Product API test goes here
	* $route are defined at app/Http/routes.php
	*/
	public function testRun()
	{
		if($this->isTestable)
		{
			$this->checkHttpStatus();
			$this->checkHttpStatus('POST');
			$this->checkHttpStatus('PUT');
		}		
	}

	private function checkHttpStatus($verb = 'GET') 
	{
		if($verb == 'GET')
		{
			$route = [
						//product
						'products', 
						'products/3630', 
						'products/brands', 
						'products/device/463',
						'products/amazone_request/3630',
						//product manager
						'pmanage_products/device/450',
						'pmanage_products/3630',
						'pmanage_products/box/10026',
						'pmanage_products/bigdata_brand/8',
						'pmanage_products'
					 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}

		if($verb == 'POST')
		{
			//
			$fillable = [
							'name' 				=> 'automatique test unit name', 
							'type' 				=> '5', 
							'subtype' 			=> '4', 
							'material' 			=> '3', 
							'brand'				=> '19', 
							'collection' 		=> '7', 
							'suppliername' 		=> 'automatique test unit supplier name', 
							'price' 			=> '1.5', 
							'price_reseller' 	=> '2.5',
							'color'				=> '3',
							'pattern'			=> '4',
							'fordevice'			=> '475',
							'supplier'			=> '10',
							'unit_test'			=> TRUE
						];

			$response = $this->call('POST', '/products', $fillable);
			$this->assertContains('{"id":', $response->getContent());
		}

		if($verb == 'PUT')
		{
			//
			$editable = [
							'id_product'		=> 753, 
							'name' 				=> 'automatique test unit update testproduct',
							'suppliername'		=> 'automatique test unit update supplier name', 
							'brand'				=> '20', 
							'supplier'			=> '11', 
							'type'				=> '6', 
							'subtype'			=> '3', 
							'pattern'			=> '5', 
							'collection'		=> '2', 
							'color'				=> '3', 
							'material'			=> '5', 
							'price_reseller'	=> '2', 
							'price'				=> '1', 
							'fordevice'			=> '3',
							'unit_test'			=> TRUE
						];

			$response = $this->call('PUT', '/products', $editable);
			$this->assertContains('{"unit_test":"success"', $response->getContent());
		}
		
	}

	private function checkValidHttpResponse($route) //valid response we need is 200
	{
		//
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}

}


