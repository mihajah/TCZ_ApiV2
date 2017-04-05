<?php
namespace App\Traits;

trait ModelGetProperties
{
	public static function getProp($prop)
	{
		return (new self)->$prop;
	}
}
?>