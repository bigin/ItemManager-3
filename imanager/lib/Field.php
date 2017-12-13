<?php namespace Imanager;

class Field
{
	public $categoryid = null;
	public $file = null;
	public $id = null;
	public $name = null;
	public $label = null;
	public $type = null;
	public $position = null;
	public $default = null;
	public $options = array();
	public $info = null;
	public $required = null;
	public $minimum = null;
	public $maximum = null;
	public $cssclass = null;
	public $configs = null;
	public $created = null;
	public $updated = null;

	public function __construct($category_id)
	{
		$this->categoryid = (int) $category_id;
		$this->file = IM_FIELDSPATH.$this->categoryid.IM_FIELDS_SUFFIX;
		$this->configs = new \stdClass();

		settype($this->id, 'integer');
		settype($this->position, 'integer');
		settype($this->maximum, 'integer');
		settype($this->required, 'boolean');
		settype($this->minimum, 'integer');
		settype($this->created, 'integer');
		settype($this->updated, 'integer');
	}

	/**
	 * Retrives field attributes array
	 */
	protected function getAttributes() {
		return array('categoryid', 'file', 'id', 'name', 'label', 'type', 'position', 'default', 'options',
			'info', 'required', 'minimum', 'maximum', 'cssclass', 'configs', 'created', 'updated');
	}

	public function set($key, $val, $sanitize=true)
	{
		$sanitizer = imanager('sanitizer');

		$key = strtolower($key);

		if(!in_array($key, $this->getAttributes())) { return false; }

		// save data depending on data type
		if($key == 'file' || $key == 'name' || $key == 'label' || $key == 'type' || $key == 'default'
			|| $key == 'info' || $key == 'areaclass' || $key == 'labelclass' || $key == 'fieldclass') {
			$this->{$key} = ($sanitizer) ? $sanitizer->text($val) : $val;
		} elseif($key == 'options') {
			$this->options[] = ($sanitizer) ? $sanitizer->text($val) : $val;
		} else {
			$this->{$key} = ($sanitizer) ? (int) $val : $val;
		}
	}


	public function get($key){ return isset($this->{$key}) ? $this->{$key} : null; }


	/**
	 * Returns maximum field id
	 */
	public function getMaximumId($xml)
	{
		$ids = array_map('intval', $xml->xpath('//fields/field/id'));
		return !empty($ids) ? max($ids) : 0;
	}


	public function save()
	{
		$sanitizer = imanager('sanitizer');

		// new file
		if(!file_exists(IM_FIELDSPATH.(int) $this->categoryid.IM_FIELDS_SUFFIX))
		{
			$newXML = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><fields><categoryid></categoryid></fields>');
			$res = $newXML->asXml(IM_FIELDSPATH.(int) $this->categoryid.IM_FIELDS_SUFFIX);
			if(empty($this->name)) return $res;
		}

		if(!$this->id && !empty($this->name))
		{
			$xml = simplexml_load_file($this->file);

			$xml->categoryid = (int)$this->categoryid;

			$id = ((int) $this->getMaximumId($xml) + 1);

			$xmlfield = $xml->addChild('field');

			$xmlfield->id = $id;
			$xmlfield->name = $sanitizer->pageName($this->name);
			$xmlfield->label = $sanitizer->text($this->label);
			$xmlfield->type = $sanitizer->text($this->type);
			$xmlfield->position = ($this->position) ? (int) $this->position : $id;
			$xmlfield->default = $this->default;

			if(!empty($this->options))
			{
				unset($xmlfield->option);
				foreach($this->options as $option) $xmlfield->option[] = $option;
			} else {
				$this->option = '';
			}

			$xmlfield->info = $this->info;
			$xmlfield->required = (boolean) $this->required;
			$xmlfield->minimum = (int) $this->minimum;
			$xmlfield->maximum = (int) $this->maximum;
			$xmlfield->cssclass = $sanitizer->text($this->cssclass);
			if(!empty($this->configs))
			{
				unset($xmlfield->configs);
				foreach($this->configs as $key => $config) $xmlfield->configs->{$key} = (string) $config;
			}
			$xmlfield->created = time();
			$xmlfield->updated = $xmlfield->created;

			return $xml->asXml(IM_FIELDSPATH.(int)$this->categoryid.IM_FIELDS_SUFFIX);

		} elseif(!empty($this->name))
		{
			$xml = simplexml_load_file($this->file);

			foreach($xml as $fieldkey => $field)
			{
				// check id exists
				foreach($field as $k => $v)
				{
					if($k == 'id' && (int) $v == (int) $this->id)
					{
						$field->name =  $sanitizer->pageName($this->name);
						$field->label =  $sanitizer->text($this->label);
						$field->type =  $sanitizer->text($this->type);
						$field->position = ($this->position) ? (int)$this->position : (int)$this->id;
						$field->default = $this->default;
						if(!empty($this->options))
						{
							unset($field->option);
							foreach($this->options as $option) $field->option[] = $option;
						} else {
							$this->option = '';
						}

						$field->info = $this->info;
						$field->required = (boolean) $this->required;
						$field->minimum = (int) $this->minimum;
						$field->maximum = (int) $this->maximum;
						$field->cssclass = $sanitizer->text($this->cssclass);
						if(!empty($this->configs))
						{
							unset($field->configs);
							foreach($this->configs as $key => $config) $field->configs->{$key} = (string) $config;
						}
						$field->created = (int) $this->created;
						$field->updated = time();
					}
				}
			}

			return $xml->asXml(IM_FIELDSPATH.(int) $this->categoryid.IM_FIELDS_SUFFIX);
		}
	}


	public function delete()
	{
		if(!$this->id) return false;

		$params = array();
		$newXML = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><fields><categoryid></categoryid></fields>');
		$xml = simplexml_load_file($this->file);

		$newXML->categoryid = $this->categoryid;

		foreach($xml as $fieldkey => $field)
		{
			// loop through the ids to except deletion fields
			foreach($field as $k => $v)
			{
				if($k == 'id' && (int) $v != (int) $this->id)
				{
					$xmlfield = $newXML->addChild('field');

					$xmlfield->id = $field->id;
					$xmlfield->name = $field->name;
					$xmlfield->label = $field->label;
					$xmlfield->type = $field->type;
					$xmlfield->position = ($field->position) ? $field->position : $field->id;
					$xmlfield->default = $field->default;
					if(!empty($field->option))
					{
						foreach($field->option as $option) $xmlfield->option[] = $option;
					} else {
						$xmlfield->option = '';
					}

					$xmlfield->info = $field->info;
					$xmlfield->required = $field->required;
					$xmlfield->minimum = $field->minimum;
					$xmlfield->created = $field->maximum;
					$xmlfield->cssclass = $field->cssclass;
					if(!empty($field->configs)) {
						foreach($field->configs as $key => $config) $xmlfield->configs->{$key} = (string) $config;
					}
					$xmlfield->created = $field->created;
					$xmlfield->updated = $field->updated;

				}
			}
		}
		unset($xml);
		return $newXML->asXml(IM_FIELDSPATH.(int) $this->categoryid.IM_FIELDS_SUFFIX);
	}


	function __destruct()
	{
		unset($this->categoryid);
		unset($this->file);
		unset($this->filename);
		unset($this->id);
		unset($this->name);
		unset($this->label);
		unset($this->type);
		unset($this->position);
		unset($this->default);
		unset($this->options);
		unset($this->info);
		unset($this->required);
		unset($this->minimum);
		unset($this->maximum);
		unset($this->cssclass);
		unset($this->created);
		unset($this->updated);
	}
}