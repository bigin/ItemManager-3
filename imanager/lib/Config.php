<?php namespace Imanager;

class Config
{
	/**
	 * Provides direct reference access to set values in the $data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * return $this
	 *
	 */
	public function __set($key, $value) {
		$this->{$key} = $value;
	}
}
?>