<?php namespace Imanager;

class Item extends FieldMapper
{
	/**
	 * @var int|null - Category id
	 */
	public $categoryid = null;

	/**
	 * @var int|null - Item id
	 */
	public $id = null;

	public $name = null;
	public $label = null;
	public $position = null;
	public $active = null;
	public $created = null;
	public $updated = null;

	public $fields = array();


	public function __construct($category_id)
	{
		$this->categoryid = (int) $category_id;

		settype($this->categoryid, 'integer');
		settype($this->id, 'integer');
		settype($this->position, 'integer');
		settype($this->active, 'boolean');
		settype($this->created, 'integer');
		settype($this->updated, 'integer');

		unset($this->fields);
		unset($this->total);

		parent::init($this->categoryid);
	}


	public static function __set_state($an_array)
	{
		$_instance = new Item($an_array['categoryid']);
		foreach($an_array as $key => $val) {
			if(is_array($val)) $_instance->{$key} = $val;
			else $_instance->{$key} = $val;
		}
		return $_instance;
	}

	/**
	 * Retrives item attributes array
	 */
	private function getAttributes() {
		return array('categoryid', 'id', 'name', 'label', 'position', 'active', 'options', 'created', 'updated');
	}

	/**
	 * Returns next available id
	 *
	 * @return int
	 */
	private function getNextId()
	{
		$ids = array();
		$maxid = 1;
		if(file_exists(IM_BUFFERPATH.'items/'.(int) $this->categoryid.'.items.php')) {
			$items = include(IM_BUFFERPATH.'items/'.(int) $this->categoryid.'.items.php');
			if(is_array($items)) { $maxid = (max(array_keys($items))+1);}
		}
		return $maxid;
	}

	/**
	 * A secure method to set the value of a field
	 *
	 * @param string $fieldname
	 * @param int|string|boolean|array $value
	 * @param bool $sanitize
	 *
	 * @return bool
	 */
	public function set($fieldname, $value, $sanitize=true)
	{
		if(!isset($this->fields[$fieldname])) {
			MsgReporter::setError('err_fieldname_exists');
			return false;
		}
		$field = $this->fields[$fieldname];

		$inputClassName = __NAMESPACE__.'\Input'.ucfirst($field->type);
		$Input = new $inputClassName($field);
		//if(!is_array($value)) {
		if(!$sanitize) {
			if(false === $Input->prepareInput($value)) { return false; }
			$this->{$fieldname} = $Input->value;
		} else {
			if(false === $Input->prepareInput($value, true)) {return false; }
			$this->{$fieldname} = $Input->value;
		}
		return true;
	}

	/**
	 * Returns any item attribut
	 *
	 * @param $key
	 *
	 * @return null
	 */
	public function get($key) { return (isset($this->$key)) ? $this->$key : null;}

	/**
	 * Save item
	 *
	 * @return bool
	 */
	public function save()
	{
		$sanitizer = imanager('sanitizer');
		$now = time();

		$this->id = (!$this->id) ? $this->getNextId() : (int) $this->id;

		if(!$this->created) $this->created = $now;
		$this->updated = $now;
		if(!$this->position) $this->position = (int) $this->id;

		// Set empty values to null
		foreach($this->fields as $key => $field) {
			if(!isset($this->{$field->name}) || !$this->{$field->name}) $this->{$field->name} = $field->default;
		}
		// Remove any other item attributes
		foreach($this as $key => $value) {
			if($key != 'fields' && !in_array($key, $this->getAttributes()) && !array_key_exists($key, $this->fields)) {
				unset($this->$key);
			}
		}

		$im = imanager()->getItemMapper();
		$im->init($this->categoryid);

		$bufferedFields = $this->fields;
		unset($this->fields);
		$im->items[$this->id] = $this;

		$export = var_export($im->items, true);
		file_put_contents($im->path, '<?php return ' . $export . '; ?>');
		$this->fields = $bufferedFields;
		return true;
	}

}