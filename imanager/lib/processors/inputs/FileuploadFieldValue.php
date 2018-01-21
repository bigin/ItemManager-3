<?php namespace Imanager;

class FileuploadFieldValue
{
	public $file = array();
	public $path = array();
	public $url = array();
	public $title = array();
	public $positions = array();

	/**
	 * This static method is called for complex field values
	 *
	 * @param $an_array
	 *
	 * @return PasswordFieldValue object
	 */
	public static function __set_state($an_array)
	{
		$_instance = new FileuploadFieldValue();
		foreach($an_array as $key => $val) {
			if(is_array($val)) $_instance->{$key} = $val;
			else $_instance->{$key} = $val;
		}
		return $_instance;
	}

}