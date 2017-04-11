<?php

class DeviceTest extends TestCase{

	/**
	* All Device API test goes here
	*/
	public function testRun()
	{
		//
		$this->checkHttpStatus();
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
					'devices'
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