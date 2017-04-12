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
		}

		if($verb == 'PUT')
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