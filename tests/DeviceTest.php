<?php

class DeviceTest extends TestCase{

	protected $isTestable = TRUE;

	/**
	* All Device API test goes here
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

		if($verb == 'POST')
		{
			//
			$fillable = [
							'name' 					=> 'automatique unit test', 
							'brand'					=> '8', 
							'id_group'				=>  0, 
							'os'					=> '80', 
							'type'					=> '2', 
							'screen_size'			=> '32', 
							'code_reference'		=> 'AUT', 
							'main_connector'		=> '15', 
							'video_output'			=> '4',
					 		'external_storage'		=> '13', 
					 		'bluetooth'				=> '6', 
					 		'nfc'					=> '7', 
					 		'ant'					=> '28', 
					 		'alternative_names'		=> 'an', 
					 		'full_references'		=> 'automation',
					 		'unit_test'				=> TRUE
						];

			$response = $this->call('POST', '/devices', $fillable);
			$this->assertContains('{"id":', $response->getContent());
		}

		if($verb == 'PUT')
		{
			//
			$editable = [
							'id_device'				=> '722',
							'name' 					=> 'automatique unit test', 
							'brand'					=> '8', 
							'id_group'				=>  0, 
							'os'					=> '80', 
							'type'					=> '2', 
							'screen_size'			=> '32', 
							'code_reference'		=> 'AUT', 
							'main_connector'		=> '15', 
							'video_output'			=> '4',
					 		'external_storage'		=> '13', 
					 		'bluetooth'				=> '6', 
					 		'nfc'					=> '7', 
					 		'ant'					=> '28', 
					 		'alternative_names'		=> 'an', 
					 		'full_references'		=> 'automation'
						];

			$response = $this->call('PUT', '/devices', $editable);
			$this->assertContains('"success":true', $response->getContent());
		}
	}

	private function checkValidHttpResponse($route)
	{
		$response = $this->call('GET', $route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}
?>