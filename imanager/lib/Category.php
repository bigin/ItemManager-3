<?php namespace Imanager;

class Category extends Object
{
	/**
	 * @var integer - Category id
	 */
	public $id = null;

	/**
	 * @var integer - Category position
	 */
	public $position = null;

	/**
	 * @var string - Name of the category
	 */
	public $name = null;

	/**
	 * @var string - Category permalink
	 */
	public $slug = null;

	/**
	 * @var integer - Category created date
	 */
	public $created = null;

	/**
	 * @var integer - Category updated date
	 */
	public $updated = null;

	/**
	 * Category constructor.
	 */
	public function __construct()
	{
		settype($this->id, 'integer');
		settype($this->position, 'integer');
		settype($this->created, 'integer');
		settype($this->updated, 'integer');

		unset($this->imanager);
	}

	/**
	 * Restricted parent init.
	 * Used to prevent the writing of external properties in field objects buffer
	 *
	 * @param $name
	 */
	public function init() { if(!isset($this->imanager)) { parent::___init();} }

	public static function __set_state($an_array)
	{
		$_instance = new Category();
		foreach($an_array as $key => $val) {
			if(is_array($val)) $_instance->{$key} = $val;
			else $_instance->{$key} = $val;
		}
		return $_instance;
	}

	/**
	 * Retrives category attributes array
	 */
	protected function getAttributes() {
		return array('id', 'position', 'name', 'slug', 'created', 'updated');
	}

	/**
	 * Get all items of the current category
	 * Call example: $category->items
	 *
	 * @param $name
	 *
	 * @return null
	 */
	public function __get($name)
	{
		if($name == 'items' && $this->id) {
			$this->init();
			$mapper = $this->imanager->itemMapper;
			$mapper->init($this->id);
			return $mapper->items;
		}
		return null;
	}

	/**
	 * @param $stat
	 * @param array $items
	 *
	 * @return mixed
	 */
	public function getItem($stat, array $items = array())
	{
		$this->init();
		$this->imanager->itemMapper->init($this->id);
		return $this->imanager->itemMapper->getItem($stat, $items);
	}

	public function getItems($stat, $length = 0, array $items = array())
	{
		$this->init();
		$this->imanager->itemMapper->init($this->id);
		return $this->imanager->itemMapper->getItems($stat, $length, $items);
	}

	public function sort($filterby = 'position', $order = 'asc',  $offset = 0, $length = 0, array $items = array())
	{
		$this->init();
		$this->imanager->itemMapper->init($this->id);
		return $this->imanager->itemMapper->sort($filterby, $order, $offset, $length, $items);
	}

	/**
	 * Set category's attribut value
	 *
	 * @param $key
	 * @param $val
	 *
	 * @return bool
	 */
	public function set($key, $val, $sanitize=true)
	{
		$this->init();
		$sanitizer = $this->imanager->sanitizer;
		$key = strtolower($key);

		// Allowed attributes
		if(!in_array($key, $this->getAttributes())) { return false; }

		if($key == 'slug') {
			$val = ($sanitize) ? $sanitizer->pageName($val) : $val;
		} elseif($key == 'id' || $key == 'created' || $key == 'updated' || $key == 'position') {
			$val = ($sanitize) ? (int) $val : $val;
		} else {
			$val = ($sanitize) ? $sanitizer->text($val) : $val;
		}
		$this->{$key} = $val;
	}

	/**
	 * Removes redundant category object attributes
	 */
	public function declutter()
	{
		foreach($this as $key => $value) {
			if(!in_array($key, $this->getAttributes())) {
				unset($this->$key);
			}
		}
	}

	/**
	 * Save category
	 *
	 * @return bool
	 */
	public function save()
	{
		$this->init();
		$sanitizer = $this->imanager->sanitizer;
		$attributes = $this->getAttributes();
		$now = time();

		$cm = $this->imanager->categoryMapper;
		$cm->init();

		if(!$this->id && $cm->categories) $this->id = (max(array_keys($cm->categories))+1);
		else $this->id = ($this->id) ? (int) $this->id : 1;

		if(!$this->created) $this->created = $now;
		$this->updated = $now;
		if(!$this->position) $this->position = (int) $this->id;

		// Clean-up category object by removing redundant object attributes
		$this->declutter();

		$cm->categories[$this->id] = $this;
		$export = var_export($cm->categories, true);
		file_put_contents($cm->path, '<?php return ' . $export . '; ?>');

		return true;
	}
}