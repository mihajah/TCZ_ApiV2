<?php
class OrderTest extends TestCase{

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
		}
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
						'orders',
						'orders/2679',
						'orders/customer/27',
						'orders/cart/2679',
						'orders/toShip',
						'orders/ean/2694'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		

		if($verb == 'POST')
		{
			//orders
			$fillable = [
							'id_customer' 	=> 8,
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/orders', $fillable);
			$this->assertContains('{"id":', $response->getContent());

			//orders/cart
			$fillable = [
							'id_order' 		=> 2691,
							'id_product'	=> 2000,
							'quantity'		=> 2,
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/orders/cart', $fillable);
			$this->assertContains('{"success":', $response->getContent());

			//orders/submit
			$fillable = [
							'id_order' 		=> 2693,
							'cart'			=> [3554 => 2],
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/orders/submit', $fillable);
			$this->assertContains('{"success":', $response->getContent());

			//orders/delivery
			$fillable = [
							'id_order' 		=> 2693,
							'shipping_fee'	=> 1,
							'delivery24'	=> 1,
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/orders/delivery', $fillable);
			$this->assertContains('{"id":', $response->getContent());

			//orders/validate
			$fillable = [
							'id_order' 		=> 2693,
							'cart'			=> [3554 => 1],
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/orders/validate', $fillable);
			$this->assertContains('{"success":', $response->getContent());

			//orders/shipped
			$fillable = [
							'id_order' 		=> 2691,
							'discount'		=> 0,
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/orders/shipped', $fillable);
			$this->assertContains('{"success":', $response->getContent());

			//orders/paid
			$fillable = [
							'id_order' 		=> 2671,
							'unit_test'		=> TRUE
						];

			$response = $this->call('POST', '/orders/paid', $fillable);
			$this->assertContains('{"success":', $response->getContent());

			//orders/rollback
			//no test needed, already used by /orders/submit
			// ...
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