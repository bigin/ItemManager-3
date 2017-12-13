<?php namespace Imanager;

class FieldMapper
{
	/**
	 * @var array of the objects of type Field
	 */
	public $fields = array();

	/**
	 * @var int - Fields counter
	 */
	public $total = 0;

	/**
	 * @var string - Path of the buffered fields
	 */
	public $path = null;

	/**
	 * @var int access permissions for new files
	 */
	protected $chmodFile = 0666;

	/**
	 * @var int access permissions for new directories
	 */
	protected $chmodDir = 0755;

	/**
	 * Init fields of a category
	 *
	 * @since 3.0
	 * @param $category_id
	 *
	 * @return array
	 */
	public function init($category_id)
	{
		$base = basename(IM_FIELDSPATH.(int) $category_id.IM_FIELDS_SUFFIX);

		$strp = strpos($base, '.');
		$file_id = substr($base, 0, $strp);

		$xml = null;
		if(file_exists(IM_FIELDSPATH.(int) $category_id.IM_FIELDS_SUFFIX)) {
			$xml = simplexml_load_file(IM_FIELDSPATH.(int) $category_id.IM_FIELDS_SUFFIX);
		}
		if(!$xml) return $this->fields;

		$i = 0;
		foreach($xml->field as $field)
		{
			$f = new Field($category_id);
			$f->options = array();
			$f->confirmed = false;
			foreach($field as $key => $val)
			{
				if(is_numeric($val)) $f->{$key} = (int) $val;
				elseif($key == 'option') $f->options[] = (string) $val;
				elseif($key == 'configs') $f->configs = $val;
				else $f->{$key} = (string) $val;
			}
			$i++;

			$this->fields[$f->name] = $f;
		}
	}

	/**
	 *
	 * @since 3.0
	 * @param $stat
	 * @param array $fields
	 *
	 * @return bool|mixed
	 */
	public function getField($stat, array $fields=array())
	{
		$locfields = !empty($fields) ? $fields : $this->fields;
		// nothing to select
		if(empty($fields)) {
			if(!$this->countFields() || $this->countFields() <= 0) { return false; }
		}

		// only id is entered
		if(is_numeric($stat)) {
			foreach($locfields as $fieldkey => $field) {
				if((int) $field->id == (int) $stat) return $field;
			}
		}

		if(false !== strpos($stat, '='))
		{
			$data = explode('=', $stat, 2);
			$key = strtolower(trim($data[0]));
			$val = trim($data[1]);
			if(false !== strpos($key, ' ')) return false;

			// Searching for the field name
			if($key == 'name') return isset($locfields[$val]) ? $locfields[$val] : false;

			foreach($locfields as $fieldkey => $field) {
				foreach($field as $k => $v) {
					// looking for the field id
					if($key == 'id' && (int) $field->id == (int) $val) return $field;
					if($key == $k && $val == $v) return $field;
				}
			}
		} else {
			if(isset($locfields[$stat])) return $locfields[$stat];
		}
		return false;
	}



	public function countFields(array $fields=array())
		{$locfields = !empty($fields) ? $fields : $this->fields; return count($locfields);}

	/**
	 * Deletes a field of category
	 *
	 * @param Field $field
	 * @return bool
	 */
	public function destroyField(Field $field, $save=true)
	{
		if($save) $this->recreateFieldsFile($field);

		unset($this->fields[$field->name]);
	}


	public function destroyFieldsFile(Category $cat)
	{
		if(file_exists(IM_FIELDS_DIR . (int) $cat->id . IM_FIELDS_FILE_SUFFIX))
		{
			return unlink(IM_FIELDS_DIR . (int) $cat->id . IM_FIELDS_FILE_SUFFIX);
		}
		return false;
	}


	private function recreateFieldsFile($field)
	{
		if(!file_exists(IM_FIELDS_DIR . (int) $field->categoryid . IM_FIELDS_FILE_SUFFIX))
			return false;
		$xml = simplexml_load_file(IM_FIELDS_DIR . (int) $field->categoryid . IM_FIELDS_FILE_SUFFIX);

		$count = 0;
		foreach($xml->field as $f)
		{
			foreach($f as $k => $v)
			{
				if($k == 'id' && (int) $v == (int) $field->id)
				{
					unset($xml->field[$count]);
					return $xml->asXml(IM_FIELDS_DIR . (int) $field->categoryid . IM_FIELDS_FILE_SUFFIX);
				}
			}
			$count++;
		}
	}




	/**
	 * Checks fields file exist on the basis of category id and create them if they don't
	 */
	public function createFields($id)
	{
		if(!file_exists(IM_FIELDSPATH.(int) $id.IM_FIELDS_SUFFIX) &&
			file_exists(IM_CATEGORYPATH.(int) $id.IM_CATEGORY_SUFFIX)) {
			$field = new Field($id);
			return $field->save();
		}
	}

	public function fieldsExists($id){ return file_exists(IM_FIELDSPATH.(int)$id.IM_FIELDS_SUFFIX); }

	public function fieldNameExists($fieldname) {
		return array_key_exists(str_replace('-', '_', imanager('sanitizer')->name($fieldname)), $this->fields);
	}



	/**
	 * Returns the array of objects of the type Field, sorted by any object attribute
	 * NOTE: However, if no $fields argument is passed to the function, the $fields
	 * must already be in the buffer: ImFields::$fields. Call the ImFields::init($category_id)
	 * method before to assign the fields to the buffer.
	 *
	 * You can sort fields by using any node
	 * Sample sortng by "position":
	 * ImFields::filterFields('position', 'DESC', $your_fields_array)
	 *
	 * @param string $filterby
	 * @param string $key
	 * @param array $fields
	 * @return boolean|array of objects of type Field
	 */
	public function filterFields($filterby, $key, array $fields=array())
	{

		$locfields = !empty($fields) ? $fields : $this->fields;
		if(empty($fields))
		{
			if(!$this->countFields() || $this->countFields() <= 0)
				return false;
		}

		$fieldcontainer = array();

		foreach($locfields as $field_id => $f)
		{
			if(!isset($f->$filterby)) continue;

			$fieldcontainer[$field_id] = $locfields[$field_id];
		}

		if(!empty($fieldcontainer))
		{
			$this->filterby = $filterby;
			usort($fieldcontainer, array($this, 'sortObjects'));
			// sorte DESCENDING
			if(strtolower($key) != 'asc') $fieldcontainer = $this->reverseFields($fieldcontainer);
			$fieldcontainer = $this->reviseFieldIds($fieldcontainer);

			if(!empty($fields))
				return $fieldcontainer;
			$this->fields = $fieldcontainer;
			return $this->fields;
		}

		return false;
	}


	public function getFieldsSaveInfo($catid, $sort=false)
	{
		$data = array();
		$xml = simplexml_load_file(IM_FIELDSPATH.(int) $catid.IM_FIELDS_SUFFIX);
		if(!$xml) return $data;
		$data['ids'] = array_map('intval', $xml->xpath('//fields/field/id'));
		$data['names'] = array_map('strval', $xml->xpath('//fields/field/name'));
		$data['types'] = array_map('strval', $xml->xpath('//fields/field/type'));
		//if($sort) {// sorts the data array by field position}
		return $data;
	}


	/**
	 * Reverse the array of fields
	 *
	 * @param array $fieldcontainer An array of objects
	 * @return boolean|array
	 */
	public function reverseFields($fieldcontainer)
	{
		if(!is_array($fieldcontainer)) return false;
		return array_reverse($fieldcontainer);
	}


	/**
	 * Revise keys of the array of fields and changes these into real field Ids
	 *
	 * @param array $fieldcontainer An array of objects
	 * @return boolean|array
	 */
	public function reviseFieldIds($fieldcontainer)
	{
		if(!is_array($fieldcontainer)) return false;
		$result = array();
		foreach($fieldcontainer as $val)
			$result[$val->name] = $val;
		return $result;
	}


	/**
	 * Sorts the objects
	 *
	 * @param $a $b objects to be sorted
	 * @return boolean
	 */
	private function sortObjects($a, $b)
	{
		$a = $a->{$this->filterby};
		$b = $b->{$this->filterby};
		if(is_numeric($a))
		{
			if($a == $b) {return 0;}
			else
			{
				if($b > $a) {return -1;}
				else {return 1;}
			}
		} else {return strcasecmp($a, $b);}
	}

	protected function install($path) {
		if(!mkdir(dirname($path), $this->chmodDir, true)) echo 'Unable to create path: '.dirname($path);
	}

}