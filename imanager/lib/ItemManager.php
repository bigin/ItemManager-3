<?php namespace Imanager;

class ItemManager extends Manager
{
	/**
	 * Just for internal use.
	 * ItemManager instances count
	 *
	 */
	public static $counter = 0;


	public function __construct() {
		self::$counter++;
		parent::__construct();
	}

	public function paginate($items, array $params = array(), $argtpls = array()) {
		return $this->templateParser->renderPagination($items, $params, $argtpls);
	}

	public function getCategories($selector, $offset = 0, $length = 0, array $categories = array()) {
		if(empty($this->categoryMapper->categories) && !$categories) {
			$this->categoryMapper->init();
		}
		return $this->categoryMapper->getCategories($selector, $offset, $length, $categories);
	}

	public function getCategory($selector, array $categories = array()) {
		if(empty($this->categoryMapper->categories) && !$categories) {
			$this->categoryMapper->init();
		}
		return $this->categoryMapper->getCategory($selector, $categories);
	}
}