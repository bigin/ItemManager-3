<?php namespace Imanager;

class Category
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
	}

	/**
	 * Retrives category attributes array
	 */
	protected function getAttributes() {
		return array('id', 'position', 'name', 'slug', 'created', 'updated');
	}

	public static function __set_state($an_array)
	{
		$_instance = new Category();
		foreach($an_array as $key => $val) {
			if(is_array($val)) $_instance->{$key} = $val;
			else $_instance->{$key} = $val;
		}
		return $_instance;
	}

	public function get($name){ return isset($this->{$name}) ? $this->{$name} : null; }

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
		$sanitizer = imanager('sanitizer');
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

	public function save()
	{
		$sanitizer = imanager('sanitizer');
		$now = time();

		$cm = imanager()->getCategoryMapper();
		$cm->init();
		// Delete redundant attributes
		foreach($this as $key => $value) {
			if(!in_array($key, $this->getAttributes())) {
				unset($this->{$key});
			}
		}
		if(!$this->id && $cm->categories) $this->id = (max(array_keys($cm->categories))+1);
		else $this->id = ($this->id) ? (int) $this->id : 1;

		if(!$this->created) $this->created = $now;
		$this->updated = $now;
		if(!$this->position) $this->position = (int) $this->id;

		$cm->categories[$this->id] = $this;
		$export = var_export($cm->categories, true);
		file_put_contents($cm->path, '<?php return ' . $export . '; ?>');

		return true;
	}
}