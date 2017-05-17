<?php
class CollectionTest extends TestCase{

	protected $isTestable = TRUE;

	/**
	* All Collection API test goes here
	* $route are defined at app/Http/routes.php
	*/
	public function testRun()
	{
		//
		if($this->isTestable)
		{
			$this->checkHttpStatus();
			$this->checkHttpStatus('POST');
			$this->checkHttpStatus('PUT');
		}	
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
						'collections',
						'collections/1'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		

		if($verb == 'POST')
		{
			//
			$fillable = [
							'collection_name' 	=> 'automatique unit test', 
							'alt_name'			=> 'automatique unit test', 
							'id_supplier'		=> '2', 
							'id_brand'			=> '1',
							'price'				=> '1.0', 
							'price_touchiz'		=> '1.5', 
							'forDeviceType'		=> '1', 
							'type'				=> '1',
							'subtype'			=> '1', 
							'material'			=> '2', 
							'pattern'			=> '2', 
							'feature1'			=> '1', 
							'feature2'			=> '2', 
							'feature3'			=> '3', 
							'feature4'			=> '4', 
							'feature5'			=> '5',
							'classic'			=> '0', 
							'DefaultColors'		=> [1, 2],
							'unit_test'			=> TRUE
						];

			$response = $this->call('POST', '/collections', $fillable);
			$this->assertContains('{"id":', $response->getContent());
		}

		if($verb == 'PUT')
		{
			//
			$editable = [
							'id_collection'		=> '58',
							'collection_name' 	=> 'automatique unit test', 
							'alt_name'			=> 'automatique unit test', 
							'id_supplier'		=> '2', 
							'id_brand'			=> '1',
							'price'				=> '1.0', 
							'price_touchiz'		=> '1.5', 
							'forDeviceType'		=> '1', 
							'type'				=> '1',
							'subtype'			=> '1', 
							'material'			=> '2', 
							'pattern'			=> '2', 
							'feature1'			=> '1', 
							'feature2'			=> '2', 
							'feature3'			=> '3', 
							'feature4'			=> '4', 
							'feature5'			=> '5',
							'classic'			=> '0', 
							'DefaultColors'		=> [1, 2],
							'unit_test'			=> TRUE
						];

			$response = $this->call('PUT', '/collections', $editable);
			$this->assertContains('{"success":true', $response->getContent());
		}
	}

	private function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>