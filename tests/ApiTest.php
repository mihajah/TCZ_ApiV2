<?php

class ApiTest extends TestCase {


	/**
	* All product API test goes here
	*/
	public function testProductApi()
	{
		$route = ['products', 'products/3630', 'products/brands'];
		foreach($route as $uri)
		{
			$this->checkValidHttpResponse($uri);
		}
	}

	private function checkValidHttpResponse($route) //valid response we need is 200
	{
		//
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}

}
