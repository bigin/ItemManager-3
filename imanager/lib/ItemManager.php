<?php namespace Imanager;

class ItemManager extends Manager
{
	/**
	 * Just for counting ItemManager instances
	 */
	public static $counter = 0;


	public function __construct() {
		self::$counter++;
		parent::__construct();
	}


	/*public function __get($name) {
		return $this->_getImAPI($name);
	}*/


	/*public function getTemplateEngine($path = '') {
		if(self::$templateEngine === null) self::$templateEngine = new TemplateEngine($path);
		return self::$templateEngine;
	}*/


	/*public function getSectionCache($path = '') {
		if(self::$sectionCache === null) self::$sectionCache = new SectionCache($path);
		return self::$sectionCache;
	}*/


	/*public function getCategoryMapper() {
		if($this->categoryMapper === null) $this->categoryMapper = new CategoryMapper();
		return self::$categoryMapper;
	}*/


	/*public function getItemMapper() {
		if(self::$itemMapper === null) self::$itemMapper = new ItemMapper();
		return self::$itemMapper;
	}*/


	/*public function getFieldMapper() {
		if(self::$fieldMapper === null) self::$fieldMapper = new FieldMapper();
		return self::$fieldMapper;
	}*/

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