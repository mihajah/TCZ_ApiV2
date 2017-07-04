<?php
class PropertyTest extends TestCase{
	
	protected $isTestable = TRUE;

	/**
	* All Supplier API test goes here
	* $route are defined at app/Http/routes.php
	*/
	public function testRun()
	{
		if($this->isTestable)
		{
			//
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
						'colors',
						'types',
						'materials',
						'features',
						'subtypes',
						'patterns',
						'devicesattr'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		

		if($verb == 'POST')
		{
			//devicesgroup
			$fillable = [
							'name' 		=> 'unit test',
							'brandID'	=> 21, 
							'unit_test'	=> TRUE
						];

			$response = $this->call('POST', '/devicesgroup', $fillable);
			$this->assertContains('{"success":{"id_group"', $response->getContent());

			//colors
			$fillable = [
							'name_fr' 	=> 'unit test',
							'name_alt'	=> 'unit_test', 
							'name_eng'	=> 'unit test',
							'code' 		=> 1254,
							'ref' 		=> 'UNT',
							'unit_test' => TRUE
						];

			$response = $this->call('POST', '/colors', $fillable);
			$this->assertContains('{"success":true', $response->getContent());

			//types
			$fillable = [
							'type_name' 	=> 'abc',
							'type_alt' 		=> 'abc',
							'type_display' 	=> 'abc',
							'type_title' 	=> 'abc',
							'html_name' 	=> 'a-b-c',
							'type_desc'		=> 'desc',
							'type_one' 		=> 'one',
							'type_filter'	=> 2,
							'unit_test' 	=> TRUE
						];

			$response = $this->call('POST', '/types', $fillable);
			$this->assertContains('{"success":true', $response->getContent());

			//material
			$fillable = [
							'material_name'	=> 'unit test',
							'unit_test' 	=> TRUE
						];

			$response = $this->call('POST', '/materials', $fillable);
			$this->assertContains('{"success":true', $response->getContent());

			//feature
			$fillable = [
							'feature_name'	=> 'unit test',
							'unit_test' 	=> TRUE
						];

			$response = $this->call('POST', '/features', $fillable);
			$this->assertContains('{"success":true', $response->getContent());

			//subType
			$fillable = [
							'subtype_name' 		=> 'abc',
							'subtype_alt' 		=> 'abc',
							'subtype_display' 	=> 'abc',
							'subtype_one' 		=> 'abc',
							'unit_test' 		=> TRUE
						];

			$response = $this->call('POST', '/subtypes', $fillable);
			$this->assertContains('{"success":true', $response->getContent());

			//pattern
			$fillable = [
							'pattern_name' 		=> 'abc',
							'unit_test' 		=> TRUE
						];

			$response = $this->call('POST', '/patterns', $fillable);
			$this->assertContains('{"success":true', $response->getContent());
		}

		if($verb == 'PUT')
		{
			//colors
			$editable 	= 	[
								'id' 		=> 1, 
								'name_fr' 	=> 'unit test', 
								'name_alt' 	=> 'unit test', 
								'name_eng' 	=> 'unit test', 
								'code' 		=> 1234, 
								'ref' 		=> 'UNT',
								'unit_test'	=> TRUE
							];

			$response = $this->call('PUT', '/colors', $editable);
			$this->assertContains('{"success":true', $response->getContent());

			//types
			$editable = 	[
								'id'			=> 123,
								'type_name' 	=> 'abc',
								'type_alt' 		=> 'abc',
								'type_display' 	=> 'abc',
								'type_title' 	=> 'abc',
								'html_name' 	=> 'a-b-c',
								'type_desc'		=> 'desc',
								'type_one' 		=> 'one',
								'type_filter'	=> 2,
								'unit_test' 	=> TRUE
							];

			$response = $this->call('PUT', '/types', $editable);
			$this->assertContains('{"success":true', $response->getContent());

			//material
			$editable = [
							'id'			=> 123,
							'material_name'	=> 'abc',
							'unit_test' 	=> TRUE
						];

			$response = $this->call('PUT', '/materials', $editable);
			$this->assertContains('{"success":true', $response->getContent());

			//feature
			$editable = [
							'id'			=> 123,
							'feature_name'	=> 'abc',
							'unit_test' 	=> TRUE
						];

			$response = $this->call('PUT', '/features', $editable);
			$this->assertContains('{"success":true', $response->getContent());

			//subtype
			$editable = [
							'id'				=> 123,
							'subtype_name' 		=> 'abc',
							'subtype_alt' 		=> 'abc',
							'subtype_display' 	=> 'abc',
							'subtype_one' 		=> 'abc',
							'unit_test' 		=> TRUE
						];

			$response = $this->call('PUT', '/subtypes', $editable);
			$this->assertContains('{"success":true', $response->getContent());

			//pattern
			$editable = [
							'id'				=> 123,
							'pattern_name' 		=> 'abc',
							'unit_test' 		=> TRUE
						];

			$response = $this->call('PUT', '/patterns', $editable);
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