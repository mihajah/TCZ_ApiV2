<?php

class ApiTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */

	public function testBasicExample()
	{
		$response = $this->call('GET', '/');

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testProductRestApi()
	{
		$response = $this->call('GET', 'products');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', 'products/3630');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', 'products/brands');
		$this->assertEquals(200, $response->getStatusCode());
	}

	private static function watcher()
	{
		//
	}

}
