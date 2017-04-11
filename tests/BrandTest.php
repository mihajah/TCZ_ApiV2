<?php
class BrandTest extends TestCase{

	protected $isTestable = TRUE;

	/**
	* All Device API test goes here
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
						'branrds'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		
	}

	private function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>