<?php
namespace App\Traits;

trait ModelGetProperties
{
	public static function getProp($prop)
	{
		return (new self)->$prop;
	}

	public static function setProp($prop, $value)
	{
		$self = new self;
		$self->$prop = $value;
	}
}
?>