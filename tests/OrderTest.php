<?php
class OrderTest extends TestCase{

	protected $isTestable = TRUE;

	/**
	* All Brand API test goes here
	* $route are defined at app/Http/routes.php
	*/
	public function testRun()
	{
		//
		if($this->isTestable)
		{
			$this->checkHttpStatus();
		}
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
						'orders',
						'orders/2679',
						'orders/customer/27',
						'orders/cart/2679',
						'orders/toShip',
						'orders/ean/2694'
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
			//
		}
	}

	private function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>