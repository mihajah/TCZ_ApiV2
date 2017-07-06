<?php 
/*
* including this file will let you to use apiv2 full functionalities outside laravel
*/

//conf
$debug = TRUE;

if($debug)
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

//dependencies
require_once __DIR__.'/bootstrap/autoload.php';
require_once __DIR__.'/bootstrap/app.php';

//launcher
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle(
	$request = Illuminate\Http\Request::capture()
);

/**
* end of file
*/
