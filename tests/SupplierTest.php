<?php
class SupplierTest extends TestCase{
	
	protected $isTestable = TRUE;

	/**
	* All Supplier API test goes here
	* $route are defined at app/Http/routes.php
	*/
	public function testRun()
	{
		if($this->isTestable)
		{
			//
			$this->checkHttpStatus();
			$this->checkHttpStatus('PUT');
		}
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
						'suppliers',
						'suppliers/5',
						'suppliers/shippingorders',
						'suppliers/ordercontent/171'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		

		if($verb == 'PUT')
		{
			//suppliers/orders
			$fillable = [
							'id_product' 	=> 3550,
							'id_supplier'	=> 3, //Test_supplier
							'qtty'			=> 2,
							'unit_test'		=> TRUE
						];

			$response = $this->call('PUT', '/suppliers/orders', $fillable);
			$this->assertContains('{"success":', $response->getContent());

			//suppliers/ordercontent
			$fillable = [
							'id_product' 	=> 3550,
							'id_order'		=> 8,
							'unit_test'		=> TRUE
						];

			$response = $this->call('PUT', '/suppliers/ordercontent', $fillable);
			$this->assertContains('{"success":', $response->getContent());
		}
	}

	private function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>