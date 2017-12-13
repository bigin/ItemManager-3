<?php namespace Imanager;

class Category
{
	/**
	 * @var integer - Category id
	 */
	public $id = null;

	/**
	 * @var string - Full file path
	 */
	public $file = null;

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
		return array('id', 'file', 'position', 'name', 'slug', 'created', 'updated');
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

		// Edit an existing category
		if(!is_null($this->id) && $this->id > 0)
		{
			$xml = simplexml_load_file($sanitizer->path($this->file));
			$this->updated = time();

			$xml->id = (int) $this->id;
			$this->id = (int) $xml->id;
			$xml->name = $sanitizer->text($this->name);
			$this->name= (string) $xml->name;
			$xml->slug = $sanitizer->pageName($this->slug);
			$this->slug = (string) $xml->slug;
			$xml->position = ($this->position) ? (int) $this->position : (int) $this->id;
			$this->position = (int) $xml->position;
			$xml->created = $sanitizer->text($this->created);
			$this->created = (int) $xml->created;
			$xml->updated = $now;
			$this->updated = (int) $xml->updated;

			if($xml->asXml($this->file)) {

				$cm = imanager()->getCategoryMapper();
				$cm->init();
				// Delete redundant attributes
				foreach($this as $key => $value) {
					if(!in_array($key, $this->getAttributes())) {
						unset($this->{$key});
					}
				}
				$cm->categories[$this->id] = $this;

				$export = var_export($cm->categories, true);
				file_put_contents($cm->path, '<?php return ' . $export . '; ?>');

				return true;
			}
		}
		// A new category
		else
		{
			$cm = imanager()->getCategoryMapper();
			$cm->init();

			$this->id = 1;
			if(!empty($cm->categories))
				$this->id = max(array_keys($cm->categories))+1;

			$this->file = $sanitizer->path(IM_CATEGORYPATH.$this->id.IM_CATEGORY_SUFFIX);

			$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><category></category>');

			$xml->name = $sanitizer->text($this->name);
			$this->name = (string) $xml->name;
			$xml->slug = $sanitizer->pageName($this->slug);
			$this->slug =  (string) $xml->slug;
			$xml->position = ($this->position) ? (int) $this->position : (int) $this->id;
			$this->position =  (int) $xml->position;
			$xml->created = time();
			$this->created =  (int) $xml->created;
			$xml->updated = $xml->created;
			$this->updated =  (int) $xml->updated;

			if($xml->asXml($this->file)) {
				// Delete redundant attributes
				foreach($this as $key => $value) {
					if(!in_array($key, $this->getAttributes())) {
						unset($this->{$key});
					}
				}
				$cm->categories[$this->id] = $this;

				$export = var_export($cm->categories, true);
				file_put_contents($cm->path, '<?php return ' . $export . '; ?>');

				return true;
			}
		}
	}
}