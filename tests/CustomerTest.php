<?php
class CustomerTest extends TestCase{

	protected $isTestable = TRUE;

	/**
	* All Customer API test goes here
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
			//
			$route = [
						'customers',
						'customers/1',
						'customers/connect/stuxat5u'
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

	protected function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>