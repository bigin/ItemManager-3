<?php namespace Imanager;

class Mapper
{
	protected $imanager = null;

	public function __construct() {
		$this->imanager = imanager();
	}
}