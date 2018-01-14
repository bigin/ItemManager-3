<?php namespace Imanager;

/**
 * Class ItemManager
 * @package Imanager
 */
class ItemManager extends Manager
{
	/**
	 * Just for internal use.
	 * ItemManager instances count
	 *
	 * @var int $counter
	 */
	public static $counter = 0;

	/**
	 * ItemManager constructor.
	 */
	public function __construct() {
		self::$counter++;
		parent::__construct();
	}

	/**
	 * A wrapper for TemplateParser's renderPagination
	 *
	 * @param $items
	 * @param array $params
	 * @param array $argtpls
	 *
	 * @return string - Pagination markup
	 */
	public function paginate($items, array $params = array(), $argtpls = array()) {
		return $this->templateParser->renderPagination($items, $params, $argtpls);
	}

	/**
	 * Categorie selector
	 *
	 * @param $selector
	 * @param int $offset
	 * @param int $length
	 * @param array $categories
	 *
	 * @return mixed|array - An array of Category objects
	 */
	public function getCategories($selector, $length = 0, array $categories = array()) {
		if(empty($this->categoryMapper->categories) && !$categories) {
			$this->categoryMapper->init();
		}
		return $this->categoryMapper->getCategories($selector, $length, $categories);
	}

	/**
	 * @param $selector
	 * @param array $categories
	 *
	 * @return mixed|Category - Catagory object
	 */
	public function getCategory($selector, array $categories = array()) {
		if(empty($this->categoryMapper->categories) && !$categories) {
			$this->categoryMapper->init();
		}
		return $this->categoryMapper->getCategory($selector, $categories);
	}

	/**
	 * Removes one of the child objects
	 *
	 * @param Item|Field|Category $obj - The object that you want to delete
	 *
	 * @return bool
	 */
	public function remove(& $obj)
	{
		if($obj instanceof Item) {
			return $this->itemMapper->remove($obj);
		}
		elseif($obj instanceof Field) {
			return $this->fieldMapper->remove($obj);
		}
		elseif($obj instanceof Category) {
			return $this->categoryMapper->remove($obj);
		}

		trigger_error('Object type is unknown', E_USER_WARNING);
		return false;
	}
}
