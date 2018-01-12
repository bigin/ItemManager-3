<?php namespace Imanager;

class ItemMapper extends Mapper
{
	/**
	 * @var string filter by node
	 */
	protected $filterby;

	/**
	 * @var boolean indicates to searchig field values
	 */
	private $fieldflag = false;

	/**
	 * @var int - Total number of items
	 */
	public $total = 0;

	/**
	 * @var null|string - Path to the items file
	 */
	public $path = null;

	/**
	 * @var - An array of the Item objects
	 */
	public $items = array();

	/**
	 * Regular init method for item objects of a category
	 *
	 * @return bool
	 */
	public function init($category_id)
	{
		parent::___init();
		$this->path = IM_BUFFERPATH.'items/'.(int) $category_id.'.items.php';

		if(!file_exists(dirname($this->path))) {
			Util::install($this->path);
		}
		if(file_exists($this->path)) {
			$this->items = include($this->path);
			$this->total = count($this->items);
			return true;
		}
		unset($this->items);
		$this->items = null;
		$this->total = 0;
		return false;
	}


	/**
	 * Initializes all items and made them available in ImItem::$items array
	 * NOTE: Could be extrem slow and memory intensive with high data volumes
	 *
	 * @return bool|mixed
	 */
	public function initAll()
	{

	}


	/**
	 * Returns a total number of given items
	 *
	 * @param array $items
	 *
	 * @return int
	 */
	public function countItems(array $items=array())
	{return !empty($items) ? count($items) : count($this->items);}


	/**
	 * Get single item
	 *
	 * @param $stat - Selector
	 * @param array $items
	 *
	 * @return bool|mixed
	 */
	public function getItem($stat, array $items=array())
	{
		if($items) $this->items = $items;
		// No items selected
		if(empty($this->items)) return false;
		// A nummeric value, id was entered?
		if(is_numeric($stat)) return !empty($this->items[$stat]) ? $this->items[$stat] : false;
		// Separate selector
		$data = explode('=', $stat, 2);
		$key = strtolower(trim($data[0]));
		$val = trim($data[1]);
		$num = substr_count($val, '%');
		$pat = false;
		if($num == 1) {
			$pos = mb_strpos($val, '%');
			if($pos == 0) { $pat = '/'.strtolower(trim(str_replace('%', '', $val))).'$/';}
			elseif($pos == mb_strlen($val)-1) {$pat = '/^'.strtolower(trim(str_replace('%', '', $val))).'/';}
		} elseif($num == 2) {
			$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'/';
		}
		if(false !== strpos($key, ' ')) return false;
		// Searching for entered value
		foreach($this->items as $itemkey => $item) {
			if(!$pat && strtolower($item->{$key}) == strtolower($val)) return $item;
			elseif($pat && preg_match($pat, strtolower($item->{$key}))) return $item;
		}
		return false;
	}


	/**
	 * Find matching item - Finds an item belonging to one category (returns exactly one result)
	 *
	 * @param $stat – A search selector: (name=Item Name) for example
	 * @param array $limit_ids – An optional parameter array, with category id's, to restrict the search process
	 *                           to specific categories (NOTE: The specifying category id's could speed up the
	 *                           searsh process!)
	 *
	 * @param array $limit_ids
	 *
	 * @return bool|mixed
	 */
	public function findItem($stat, array $limit_ids = array())
	{
		$mapper = imanager()->getCategoryMapper();
		if(!empty($limit_ids))
		{
			foreach($limit_ids as $catid) {
				$this->init($mapper->categories[(int)$catid]->id);
				$item = $this->getItem($stat);
				if(!empty($item)) return $item;
			}
			return false;
		}
		foreach($mapper->categories as $category)
		{
			$this->init($category->id);
			$item = $this->getItem($stat);
			if(!empty($item)) return $item;
		}
		return false;
	}


	/**
	 * Find matching items - Finds all items belonging to one category (returns matching items of a category)
	 *
	 * @param $stat – A search selector: (name=Item Name) for example
	 * @param array $limit_ids – An optional parameter array, with category id's, to restrict the search process
	 *                           to specific categories (NOTE: The specifying category id's could speed up the
	 *                           searsh process!)
	 *
	 * @return array|bool
	 */
	public function findItems($stat, array $limit_ids = array())
	{
		$mapper = imanager()->getCategoryMapper();
		if(!empty($limit_ids))
		{
			foreach($limit_ids as $catid) {
				$this->init($mapper->categories[(int)$catid]->id);
				$items = $this->getItems($stat);
				if(!empty($items)) return $items;
			}
			return false;
		}
		foreach($mapper->categories as $category)
		{
			$this->init($category->id);
			$items = $this->getItems($stat);
			if(!empty($items)) return $items;
		}
		return false;
	}


	/**
	 * Find all matching items - Finds all items of all categories (returns matching items of all categories)
	 *
	 * @param $stat – A search selector: (name=Item Name) for example
	 * @param array $limit_ids – An optional parameter array, with category id's, to restrict the search process
	 *                           to specific categories (NOTE: The specifying category id's could speed up the
	 *                           searsh process!)
	 *
	 * @return array|bool
	 */
	public function findAll($stat, array $limit_ids = array())
	{
		$allItems = array();
		$count = 0;
		$mapper = imanager()->getCategoryMapper();
		if(!empty($limit_ids))
		{
			foreach($limit_ids as $catid) {
				$this->init($mapper->categories[(int)$catid]->id);
				$items = $this->getItems($stat);
				$count += $this->total;
				if(!empty($items)) $allItems[] = $items;
			}
			$this->total = $count;
			return (!empty($allItems) ? $allItems : false);
		}
		foreach($mapper->categories as $category)
		{
			$this->init($category->id);
			$items = $this->getItems($stat);
			$count += $this->total;
			if(!empty($items)) $allItems[] = $items;
		}
		$this->total = $count;
		return (!empty($allItems) ? $allItems : false);
	}

	/**
	 * Select method for multiple items
	 *
	 * @param $stat        - Selector
	 * @param int $length  - A clause that is used to specify the number of records to return
	 * @param array $items - Item array rekursion
	 *
	 * @return array|bool
	 */
	public function getItems($stat, $length = 0, array $items = array())
	{
		$offset = 0;//($this->imanager->input->pageNumber) ? (($this->imanager->input->pageNumber -1) * $length +1) : 0;
		settype($length, 'integer');
		// reset offset
		$offset = ($offset > 0) ? $offset-1 : $offset;

		//if($offset > 0 && $length > 0 && $offset >= $length) return false;

		if(!$items) $items = $this->items;

		// nothing to select
		if(empty($items)) return false;

		$treads = array();
		// All parameter have to match the data
		if(false !== strpos($stat, '&&'))
		{
			$treads = explode('&&', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part) {
				$sepitems[] = $this->applySearchPattern($items, $part);
			}
			if(!empty($sepitems[0]) && !empty($sepitems[1]))
			{
				$arr = array_map('unserialize', array_intersect(array_map('serialize', $sepitems[0]), array_map('serialize', $sepitems[1])));

				// limited output
				if(!empty($arr) && ($offset > 0 || $length > 0)) {
					//if($length == 0) $len = null;
					$arr = array_slice($arr, $offset, $length, true);
				}
				return $this->reviseItemIds($arr);
			}
			// only one parameter have to match the data
		} elseif(false !== strpos($stat, '||'))
		{
			$treads = explode('||', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part)
			{
				$sepitems[] = $this->applySearchPattern($items, $part);
			}
			if(!empty($sepitems[0]) || !empty($sepitems[1]))
			{
				if(is_array($sepitems[0]) && is_array($sepitems[1]))
				{
					// limited output
					if(!empty($sepitems[0]) && ($offset > 0 || $length > 0)) {
						//if($length == 0) $len = null;
						$sepitems[0] = array_slice($sepitems[0], $offset, $length, true);
						$sepitems[1] = array_slice($sepitems[1], $offset, $length, true);
						$return = array_merge($sepitems[0], $sepitems[1]);
						return $this->reviseItemIds(array_slice($return, $offset, $length, true));
					}
					return $this->reviseItemIds(array_merge($sepitems[0], $sepitems[1]));

				} elseif(is_array($sepitems[0]) && !is_array($sepitems[1]))
				{
					// limited output
					if(!empty($sepitems[0]) && ($offset > 0 || $length > 0)) {
						//if($length == 0) $len = null;
						$sepitems[0] = array_slice($sepitems[0], $offset, $length, true);
					}
					return $this->reviseItemIds($sepitems[0]);
				} else
				{
					// limited output
					if(!empty($sepitems[1]) && ($offset > 0 || $length > 0)) {
						//if($length == 0) $len = null;
						$sepitems[1] = array_slice($sepitems[1], $offset, $length, true);
					}
					return $this->reviseItemIds($sepitems[1]);
				}
			}
			// If $stat contains only one or empty selector
		} else
		{
			if(!empty($stat)) $arr = $this->applySearchPattern($items, $stat);
			else $arr = $items;
			// limited output
			if(!empty($arr) && ($offset > 0 || $length > 0)) {
				//if($length == 0) $len = null;
				$arr = array_slice($arr, $offset, $length, true);
			}

			return $this->reviseItemIds($arr);
		}
		return false;
	}


	/**
	 * A public method for sorting the items
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

		$offset = ($offset) ? $offset :
			(($this->imanager->input->pageNumber) ? (($this->imanager->input->pageNumber -1) * $length) : 0);

		/*$offset = ($offset > 0) ? $offset-1 : (($this->imanager->input->pageNumber) ?
			(($this->imanager->input->pageNumber -1) * $length +1) : 1);*/

		$localItems = !empty($items) ? $items : $this->items;

		if(empty($localItems)) return false;

		$this->filterby = ($filterby) ? $filterby : $this->imanager->config->filterByItems;

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

		$this->items = $localItems;
		return $this->items;
	}

	/**
	 * Select items by using several search patterns
	 *
	 * @param array $items - An array of categories to be processed
	 * @param $stat - Selector
	 *
	 * @return array|bool
	 */
	protected function applySearchPattern(array $items, $stat)
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
	 * Sorts the item objects
	 *
	 * @param $a $b objects to be sorted
	 * @return boolean
	 */
	protected function sortObjects($a, $b)
	{
		$a = $a->{$this->filterby};
		$b = $b->{$this->filterby};
		if(is_numeric($a)) {
			if($a == $b) {return 0;}
			else {
				if($b > $a) {return -1;}
				else {return 1;}
			}
		} else {return strcasecmp($a, $b);}
	}

	/**
	 * Reverse the array of items
	 *
	 * @param array $itemsContainer An array of objects
	 * @return boolean|array
	 */
	public function reverseItems($itemsContainer)
	{
		if(!is_array($itemsContainer)) return false;
		return array_reverse($itemsContainer);
	}

	/**
	 * Revise keys of the array of items and changes these into real item id's
	 *
	 * @param array $itemsContainer An array of objects
	 * @return boolean|array
	 */
	public function reviseItemIds($itemsContainer)
	{
		if(!is_array($itemsContainer)) return false;
		$result = array();
		foreach($itemsContainer as $val) { $result[$val->id] = $val; }
		return $result;
	}
}