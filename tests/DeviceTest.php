<?php

class DeviceTest extends TestCase{

	/**
	* All Device API test goes here
	* $route are defined at app/Http/routes.php
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
						'devices',
						'devices/450',
						'devices/brand/8',
						'devices/brand/8/ignore',
						'devices/phone/brand/8',
						'devices/phone/brand/8/ignore',
						'devices/tablet/brand/8',
						'devices/tablet/brand/8/ignore'
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