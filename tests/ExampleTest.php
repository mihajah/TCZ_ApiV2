<?php

class ExampleTest extends TestCase {

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

	public function testOneCollection()
	{
		$response = $this->call('GET', 'collections/1');
		$this->assertEquals(200, $response->getStatusCode());
	}

}
