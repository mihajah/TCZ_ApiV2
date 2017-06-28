<?php
Class ReliquatTest extends TestCase{

	protected $isTestable = TRUE;

	/**
	* All Reliquat API test goes here
	* $route are defined at app/Http/routes.php
	*/
	public function testRun()
	{
		if($this->isTestable)
		{
			$this->checkHttpStatus();
			$this->checkHttpStatus('POST');
		}
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			//
			$route = [
						'reliquats',
						'reliquats/2',
						'reliquats/customer/8',
						'reliquats/order/3211'
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
							'id_customer'   => 8, 
							'id_order'      => 2530, 
							'id_product'    => 3630, 
							'qty_initial'   => 2, 
							'qty_sent'      => 1, 
							'qty_left'      => 1,
							'unit_test'     => TRUE
						];

			$response = $this->call('POST', '/reliquats', $fillable);
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