<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {

	protected $except = [
							'products',
							'products/*',
							'devices',
							'devices/*',
							'collections',
							'collections/*',
							'customers',
							'customers/*',
							'calls',
							'calls/*',
							'orders',
							'orders/*',
							'suppliers/*',
							'stocks',
							'stocks/*',
							'devicesgroup',
							'colors',
							'types',
							'materials',
							'features',
							'subtypes',
							'patterns',
							'reliquats',
							'reliquats/*',
							'invoice'
						];

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		foreach($this->except as $route) 
		{
		      if($request->is($route)) 
		      {
		        	return $next($request);
		      }
	    }

		return parent::handle($request, $next);
	}

}
