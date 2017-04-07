<?php
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	use DatabaseMigrations;
	
	public function testBasicExample()
	{
		$response = $this->call('GET', '/');

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testOneCollection()
	{
		$response = $this->call('GET', 'collections/1');
		print_r($response->getContent()); 
		$this->assertEquals(200, $response->getStatusCode());
	}

	private static function watcher()
	{
		//
	}

}
