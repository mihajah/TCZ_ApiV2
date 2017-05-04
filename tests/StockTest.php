<?php
Class StockTest extends TestCase{
	
	protected $isTestable = TRUE;

	/**
	* All Stock API test goes here
	* $route are defined at app/Http/routes.php
	*/
	public function testRun()
	{
		//
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
						'stocks/3630',
						'stocks/tracker/unit_test'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}

		if($verb == 'POST')
		{
			//
		}

		if($verb == 'PUT')
		{
			//stocks
			$editable = [
							'id_customer' 	=> 3554,
							'qty_real'		=> 3,
							'reason'		=> 2,
							'unit_test'		=> TRUE
						];

			$response = $this->call('PUT', 'stocks', $editable);
			$this->assertContains('{"success":', $response->getContent());

			//stocks/inventory
			$editable = [
							'id_product' 	=> 3630,
							'sph'			=> 100,
							'unit_test'		=> TRUE
						];

			$response = $this->call('PUT', 'stocks/inventory', $editable);
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