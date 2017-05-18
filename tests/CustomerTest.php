<?php
class CustomerTest extends TestCase{

	protected $isTestable = TRUE;

	/**
	* All Customer API test goes here
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
			//
			$route = [
						'customers',
						'customers/1',
						'customers/connect/stuxat5u'
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
							'named' 		=> 'automatique unit test', 
							'enseigne'		=> 'aut', 
							'adresse'		=> 'adresse test', 
							'adresse_pc'	=> '7500', 
							'adresse_ville'	=> 'ville', 
							'adresse_pays'	=> 'france', 
							'phone1'		=> '123456789', 
							'firstname'		=> 'fi', 
							'lastname'		=> 'ln',
					 		'email'			=> 'raw@mail.fr', 
					 		'name'			=> 'namesix', 
					 		'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/customers', $fillable);
			$this->assertContains('{"id":', $response->getContent());
		}

		if($verb == 'PUT')
		{
			//
			$editable = [
							'id_customer'	=> 247,
							'to_callback' 	=> '0', 
							'status'		=> '0', 
							'newsletter'	=> 1, 
							'notes'			=> urlencode('my str'),  
					 		'unit_test'		=> TRUE
						];

			$response = $this->call('PUT', '/customers', $editable);
			$this->assertContains('{"id":', $response->getContent());
		}
	}

	protected function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>