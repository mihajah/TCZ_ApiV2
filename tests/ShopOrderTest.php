<?php
class ShopOrderTest extends TestCase{
	
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
			//$this->checkHttpStatus('POST');
			//$this->checkHttpStatus('PUT');
		}
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
						'shoporders/5'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		

		if($verb == 'PUT')
		{
			//
		}

		if($verb == 'POST')
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