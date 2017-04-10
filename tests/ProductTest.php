<?php

class ProductTest extends TestCase {


	/**
	* All Product API test goes here
	*/
	public function testRun()
	{
		$this->checkHttpStatus();
		$this->checkHttpStatus('POST');
	}

	private function checkHttpStatus($verb = 'GET') 
	{
		if($verb == 'GET')
		{
			$route = [
						'products', 
						'products/3630', 
						'products/brands', 
						'products/device/463',
						'products/amazone_request/3630'
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
		}
		
	}

	private function checkValidHttpResponse($route) //valid response we need is 200
	{
		//
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}

}
