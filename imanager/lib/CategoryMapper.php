<?php namespace Imanager;

class CategoryMapper extends Mapper
{

	/**
	 * @var string - Filter by attribute
	 */
	private $filterby;

	/**
	 * @var int - Categories counter
	 */
	public $total = 0;

	/**
	 * @var null|string - Category file path
	 */
	public $path = null;

	/**
	 * Initializes all the categories and made them available in ImCategory::$categories buffer
	 */
	public function init()
	{
		parent::___init();
		$this->path = IM_BUFFERPATH.'/categories/categories.php';
		if(!file_exists(dirname($this->path))) {
			Util::install($this->path);
		}
		if(file_exists($this->path)) {
			$this->categories = include($this->path);
			$this->total = count($this->categories);
			return true;
		}
		unset($this->categories);
		$this->categories = null;
		$this->total = 0;
		return false;
	}


	/**
	 * Returns the number of categories
	 *
	 * @param array $categories
	 * @return int
	 */
	public function countCategories(array $categories=array()) {
		return count(!empty($categories) ? $categories : $this->categories);
	}


	/**
	 * Returns the object of type Category
	 * NOTE: However if no $categories argument is passed to the function, the categories
	 * must already be in the buffer: ImCategory::$categories. Call the ImCategory::init()
	 * method before to assign the categories to the buffer.
	 *
	 * You can search for category by ID: ImCategory::getCategory(2) or similar to ImCategory::getCategory('id=2')
	 * or by category name ImCategory::getCategory('name=My category name')
	 *
	 * @param string/integer $stat
	 * @param array $categories
	 * @return boolean|object of the type Category
	 */
	public function getCategory($stat, array $categories=array())
	{
		if(!$categories)  $categories = $this->categories;
		// No items selected
		if(empty($categories)) return null;
		// A nummeric value, id was entered?
		if(is_numeric($stat)) return !empty($categories[$stat]) ? $categories[$stat] : null;
		// Separate selector
		$data = explode('=', $stat, 2);
		$key = strtolower(trim($data[0]));
		$val = trim($data[1]);
		$num = substr_count($val, '%');
		$pat = false;
		if($num == 1) {
			$pos = mb_strpos($val, '%');
			if($pos == 0) {
				$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'$/';
			}
			elseif($pos == mb_strlen($val)-1) {
				$pat = '/^'.strtolower(trim(str_replace('%', '', $val))).'/';
			}
		} elseif($num == 2) {
			$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'/';
		}
		if(false !== strpos($key, ' ')) return null;
		// Searching for entered value
		foreach($categories as $itemkey => $item) {
			if(!$pat && strtolower($item->{$key}) == strtolower($val)) return $item;
			elseif($pat && preg_match($pat, strtolower($item->{$key}))) return $item;
		}
		return null;
	}


	/**
	 * Returns the array of objects type Category, by a comparison of values
	 * NOTE: However if no $categories argument is passed to the function, the categories
	 * must already be in the buffer: ImCategory::$categories. Call the ImCategory::init()
	 * method before to assign the categories to the buffer.
	 *
	 * You can sort categories by using any node
	 * Sample sortng by "position":
	 * ImCategory::filterCategories('position', 'DESC', $your_categories_array)
	 *
	 * @param string $filterby
	 * @param string $key
	 * @param array $categories
	 * @return boolean|array
	 */
	public function getCategories($stat, $offset = 0, $length = 0, array $categories=array())
	{
		settype($offset, 'integer');
		settype($length, 'integer');
		// reset offset
		$offset = ($offset > 0) ? $offset-1 : $offset;

		if($offset > 0 && $length > 0 && $offset >= $length) return null;

		if(!$categories) $categories = $this->categories;

		// nothing to select
		if(empty($categories)) return null;

		$treads = array();
		// All parameter have to match selector
		if(false !== strpos($stat, '&&')) {
			$treads = explode('&&', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part) {
				$sepitems[] = $this->separateCategories($categories, $part);
			}
			if(!empty($sepitems[0]) && !empty($sepitems[1])) {
				$arr = array_map('unserialize', array_intersect(array_map('serialize', $sepitems[0]), array_map('serialize', $sepitems[1])));
				// limited output
				if(!empty($arr) && ( $offset > 0 ||  $length > 0)) {
					//if( $length == 0) $len = null;
					$arr = array_slice($arr, $offset, $length, true);
				}
				return $arr;
			}
		// Only one parameter have to match the data
		} elseif(false !== strpos($stat, '||'))
		{
			$treads = explode('||', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part)
			{
				$sepitems[] = $this->separateCategories($categories, $part);
			}
			if(!empty($sepitems[0]) || !empty($sepitems[1]))
			{
				if(is_array($sepitems[0]) && is_array($sepitems[1])) {
					// limited output
					if(!empty($sepitems[0]) && ( $offset > 0 ||  $length > 0)) {
						//if( $length == 0) $len = null;
						$sepitems[0] = array_slice($sepitems[0], $offset, $length, true);
						$sepitems[1] = array_slice($sepitems[1], $offset, $length, true);
						$return = array_merge($sepitems[0], $sepitems[1]);
						return array_slice($return, $offset, $length, true);
					}
					return array_merge($sepitems[0], $sepitems[1]);

				} elseif(is_array($sepitems[0]) && !is_array($sepitems[1])) {
					// limited output
					if(!empty($sepitems[0]) && ($offset > 0 || $length > 0)) {
						//if( $length == 0) $len = null;
						$sepitems[0] = array_slice($sepitems[0],  $offset,  $length, true);
					}
					return $sepitems[0];
				} else
				{
					// limited output
					if(!empty($sepitems[1]) && ( $offset > 0 ||  $length > 0)) {
						//if( $length == 0) $len = null;
						$sepitems[1] = array_slice($sepitems[1], $offset, $length, true);
					}
					return $sepitems[1];
				}
			}
		// If $stat contains only one or empty selector
		} else
		{
			if(!empty($stat)) $arr = $this->separateCategories($categories, $stat);
			else $arr = $categories;
			// limited output
			if(!empty($arr) && ($offset > 0 || $length > 0)) {
				//if( $length == 0) $len = null;
				$arr = array_slice($arr, $offset, $length, true);
			}

			return $arr;
		}
		return null;
	}

	/**
	 * Separate categories search selectors and
	 * compare these values.
	 *
	 * @param array $items - An array of categories to be processed
	 * @param $stat - Selector
	 *
	 * @return array|bool
	 */
	protected function separateCategories(array $items, $stat)
	{
		$res = array();
		$pattern = array(0 => '>=', 1 => '<=', 2 => '!=', 3 => '>', 4 => '<', 5 => '=');

		foreach($pattern as $pkey => $pval)
		{
			if(false === strpos($stat, $pval)) continue;

			$data = explode($pval, $stat, 2);
			$key = strtolower(trim($data[0]));
			$val = trim($data[1]);
			if(false !== strpos($key, ' ')) return false;

			$num = substr_count($val, '%');
			$pat = false;
			if($num == 1) {
				$pos = mb_strpos($val, '%');
				if($pos == 0) {
					$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'$/';
				} elseif($pos == (mb_strlen($val)-1)) {
					$pat = '/^'.strtolower(trim(str_replace('%', '', $val))).'/';
				}
			} elseif($num == 2) {
				$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'/';
			}

			foreach($items as $itemkey => $item)
			{
				//if(!array_key_exists($key, $item)) { continue; }
				if($pkey == 0) {
					if($item->$key < $val) continue;
				} elseif($pkey == 1) {
					if($item->$key > $val) continue;
				} elseif($pkey == 2) {
					if($item->$key == $val) continue;
				} elseif($pkey == 3) {
					if($item->$key <= $val) continue;
				} elseif($pkey == 4) {
					if($item->$key >= $val) continue;
				} elseif($pkey == 5) {
					if($item->$key != $val && !$pat) { continue; }
					elseif($pat && !preg_match($pat, strtolower($item->$key))){ continue; }
				}
				$res[$item->id] = $item;
			}

			if(!empty($res)) return $res;
			return false;
		}
		return false;
	}

	/**
	 * A public method for sorting the categories
	 *
	 * You can sort items by using any attribute
	 * Default sortng attribute is "position":
	 * FieldMapper::sort('position', 'DESC', $offset, $length, $your_items_array)
	 *
	 * @param string $filterby
	 * @param string $order
	 * @param array $items
	 *
	 * @return boolean|array of Field objects
	 */
	public function sort($filterby = null, $order = 'asc',  $offset = 0, $length = 0, array $items = array())
	{
		settype($offset, 'integer');
		settype($length, 'integer');

		$offset = ($offset > 0) ? $offset-1 : $offset;

		$localItems = !empty($items) ? $items : $this->categories;

		if(empty($localItems)) return false;

		$this->filterby = ($filterby) ? $filterby : $this->imanager->config->filterByCategories;

		usort($localItems, array($this, 'sortObjects'));
		// sort DESCENDING
		if(strtolower($order) != 'asc') $localItems = $this->reverseItems($localItems);
		$localItems = $this->reviseItemIds($localItems);

		// Limiting item number
		if(!empty($localItems) && ($offset > 0 || $length > 0))
		{
			//if($length == 0) $len = null;
			$localItems = array_slice($localItems, $offset, $length, true);
		}

		if(!empty($items)) return $localItems;

		$this->categories = $localItems;
		return $this->categories;
	}


	/**
	 * Deletes the category
	 *
	 * @param Category $cat
	 * @return bool
	 */
	/*public function destroyCategory(Category $cat)
	{
		if(file_exists(IM_CATEGORYPATH . $cat->id . IM_CATEGORY_SUFFIX))
			return unlink(IM_CATEGORYPATH . $cat->id . IM_CATEGORY_SUFFIX);
		return false;
	}*/


	/**
	 * Reverse the array of items
	 *
	 * @param array $itemContainer An array of objects
	 * @return boolean|array
	 */
	public function reverseItems($itemContainer)
	{
		if(!is_array($itemContainer)) return false;
		return array_reverse($itemContainer);
	}


	/**
	 * Revise keys of the array of categories and changes these into real item id's
	 *
	 * @param array $itemcontainer An array of objects
	 * @return boolean|array
	 */
	public function reviseItemIds($itemContainer)
	{
		if(!is_array($itemContainer)) return false;
		$result = array();
		foreach($itemContainer as $val) { $result[$val->id] = $val; }
		return $result;
	}



	/**
	 * Sorts the category objects
	 *
	 * @param $a $b objects to be sorted
	 * @return boolean
	 */
	private function sortObjects($a, $b)
	{
		$a = $a->{$this->filterby};
		$b = $b->{$this->filterby};
		if(is_numeric($a)) {
			if($a == $b) {return 0;}
			else{
				if($b > $a) {return -1;}
				else {return 1;}
			}
		} else {return strcasecmp($a, $b);}
	}


	public function pagination(array $params = array(), $argtpls = array())
	{

		$tpl = $this->imanager->getTemplateEngine();
		$config = $this->imanager->config;

		$pagination = $tpl->getTemplates('pagination');
		$tpls['wrapper'] = !empty($argtpls['wrapper']) ? $argtpls['wrapper'] : $tpl->getTemplate('wrapper', $pagination);
		$tpls['prev'] = !empty($argtpls['prev']) ? $argtpls['prev'] : $tpl->getTemplate('prev', $pagination);
		$tpls['prev_inactive'] = !empty($argtpls['prev_inactive']) ? $argtpls['prev_inactive'] : $tpl->getTemplate('prev_inactive', $pagination);
		$tpls['central'] = !empty($argtpls['central']) ? $argtpls['central'] : $tpl->getTemplate('central', $pagination);
		$tpls['central_inactive'] = !empty($argtpls['central_inactive']) ? $argtpls['central_inactive'] : $tpl->getTemplate('central_inactive', $pagination);
		$tpls['next'] = !empty($argtpls['next']) ? $argtpls['next'] : $tpl->getTemplate('next', $pagination);
		$tpls['next_inactive'] = !empty($argtpls['next_inactive']) ? $argtpls['next_inactive'] : $tpl->getTemplate('next_inactive', $pagination);
		$tpls['ellipsis'] = !empty($argtpls['ellipsis']) ? $argtpls['ellipsis'] : $tpl->getTemplate('ellipsis', $pagination);
		$tpls['secondlast'] = !empty($argtpls['secondlast']) ? $argtpls['secondlast'] : $tpl->getTemplate('secondlast', $pagination);
		$tpls['second'] = !empty($argtpls['second']) ? $argtpls['second'] : $tpl->getTemplate('second', $pagination);
		$tpls['last'] = !empty($argtpls['last']) ? $argtpls['last'] : $tpl->getTemplate('last', $pagination);
		$tpls['first'] = !empty($argtpls['first']) ? $argtpls['first'] : $tpl->getTemplate('first', $pagination);

		$page = (!empty($params['page']) ? $params['page'] : (isset($_GET['page']) ? (int) $_GET['page'] : 1));
		$params['items'] = !empty($params['count']) ? $params['count'] : $this->total;
		$pageurl = !empty($params['pageurl']) ? $params['pageurl'] : 'page=';
		$start = !empty($params['start']) ? $params['start'] : 1;

		$maxitemperpage = ((int) $config->backend->maxcatperpage > 0) ?
			$config->backend->maxcatperpage : 20;
		$limit = !empty($params['limit']) ? $params['limit'] : $config->backend->maxcatperpage;
		$adjacents = !empty($params['adjacents']) ? $params['adjacents'] : 3;
		$lastpage = !empty($params['lastpage']) ? $params['lastpage'] : ceil($params['items'] / $maxitemperpage);

		$next = ($page+1);
		$prev = ($page-1);

		//$tpl->init();
		// only one page to show
		if($lastpage <= 1)
			return $tpl->render($tpls['wrapper'], array('value' => ''), true);

		$output = '';

		if($page > 1)
			$output .= $tpl->render($tpls['prev'], array('href' => $pageurl . $prev), true);
		else
			$output .= $tpl->render($tpls['prev_inactive'], array(), true);

		// not enough pages to bother breaking it up
		if($lastpage < 7 + ($adjacents * 2))
		{
			for($counter = 1; $counter <= $lastpage; $counter++)
			{
				if($counter == $page)
				{
					$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
				} else
				{
					$output .= $tpl->render($tpls['central'], array(
							'href' => $pageurl . $counter, 'counter' => $counter), true
					);
				}
			}
			// enough pages to hide some
		} elseif($lastpage > 5 + ($adjacents * 2))
		{
			// vclose to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))
			{
				for($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if($counter == $page)
					{
						$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
					} else
					{
						$output .= $tpl->render($tpls['central'], array('href' => $pageurl . $counter,
							'counter' => $counter), true);
					}
				}
				// ...
				$output .= $tpl->render($tpls['ellipsis']);
				// sec last
				$output .= $tpl->render($tpls['secondlast'], array('href' => $pageurl . ($lastpage - 1),
					'counter' => ($lastpage - 1)), true);
				// last
				$output .= $tpl->render($tpls['last'], array('href' => $pageurl . $lastpage,
					'counter' => $lastpage), true);
			}
			// middle pos; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				// first
				$output .= $tpl->render($tpls['first'], array('href' => $pageurl . '1'), true);
				// second
				$output .= $tpl->render($tpls['second'], array('href' => $pageurl . '2', 'counter' => '2'), true);
				// ...
				$output .= $tpl->render($tpls['ellipsis']);

				for($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if($counter == $page)
					{
						$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
					} else
					{
						$output .= $tpl->render($tpls['central'], array('href' => $pageurl . $counter,
							'counter' => $counter), true);
					}
				}
				// ...
				$output .= $tpl->render($tpls['ellipsis']);
				// sec last
				$output .= $tpl->render($tpls['secondlast'], array('href' => $pageurl . ($lastpage - 1),
					'counter' => ($lastpage - 1)), true);
				// last
				$output .= $tpl->render($tpls['last'], array('href' => $pageurl . $lastpage,
					'counter' => $lastpage), true);
			}
			//close to end; only hide early pages
			else
			{
				// first
				$output .= $tpl->render($tpls['first'], array('href' => $pageurl . '1'), true);
				// second
				$output .= $tpl->render($tpls['second'], array('href' => $pageurl . '2', 'counter' => '2'), true);
				// ...
				$output .= $tpl->render($tpls['ellipsis']);

				for($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if($counter == $page)
					{
						$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
					} else
					{
						$output .= $tpl->render($tpls['central'], array('href' => $pageurl . $counter,
							'counter' => $counter), true);
					}
				}
			}
		}
		//next link
		if($page < $counter - 1)
			$output .= $tpl->render($tpls['next'], array('href' => $pageurl . $next), true);
		else
			$output .= $tpl->render($tpls['next_inactive'], array(), true);

		return $tpl->render($tpls['wrapper'], array('value' => $output), true);
	}
}