<?php
Class CallTest extends TestCase{
	
	protected $isTestable = TRUE;

	/**
	* All Brand API test goes here
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
						'calls',
						'calls/636',
						'calls/customer/177',
						'calls/lastcall/customer/177'
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
							'id_customer' 	=> 177,
							'note'			=> urlencode('travis test'),
							'date'			=> date('Y-m-d H:i:s'),
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/calls', $fillable);
			$this->assertContains('{"id":', $response->getContent());
		}

		if($verb == 'PUT')
		{
			//
			$editable = [
							'id' 			=> 644,
							'note'			=> urlencode('don\'t delete, used by travis ci'),
							'date'			=> date('Y-m-d H:i:s'),
							'status'		=> 1,
							'flag'			=> 1,
							'unit_test'		=> TRUE
						];

			$response = $this->call('PUT', '/calls', $editable);
			$this->assertContains('{"id":', $response->getContent());
		}
	}

	private function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>