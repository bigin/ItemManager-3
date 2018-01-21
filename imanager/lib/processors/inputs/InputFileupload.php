<?php namespace Imanager;

class InputFileupload implements InputInterface
{
	protected $value;

	protected $field;

	protected $itemid;

	protected $tmpDir = null;

	protected $timestamp;

	public $errorCode = null;

	public $errorBuffer = array();

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->value = new FileuploadFieldValue();

		$this->timestamp = time();
		//$this->tmpDir = IM_UPLOADPATH.'.tmp_'.$this->timestamp.'_'
		/*$this->values->file_name = array();
		$this->values->path = array();
		$this->values->fullpath = array();
		$this->values->url = array();
		$this->values->fullurl = array();
		$this->values->title = array();
		$this->positions = array();
		$this->titles = array();*/
	}


	public function __set($name, $value) { $this->$name = $value; }


	public function prepareInput($values, $sanitize = false)
	{
		if(!is_array($values)) {
			$this->errorCode = self::WRONG_VALUE_FORMAT;
			return false;
		}

		// Check outside coming data for correctness
		foreach($values as $item) {
			if(!$this->field->categoryid) {
				$this->errorCode = self::UNDEFINED_CATEGORY_ID;
				return false;
			}

			$categoryid = (int) $this->field->categoryid;
			$itemid = $this->itemid;

			// The value must be an array
			if(!is_array($item)) {
				$this->errorCode = self::WRONG_VALUE_FORMAT;
				return false;
			}

			// Loop through all items now and move them to the right location
			foreach($item as $key => $value) {

				if(!file_exists($value['path'])) {
					$errorBuffer[$key]['message'] = 'File does not exist';
					continue;
				}
			}

		}

		echo $this->itemid;

		//Util::preformat($values);


		// imageupload
		/*if($fieldvalue->type == 'imageupload' || $fieldvalue->type == 'fileupload')
		{
			// new item
			if(empty($_GET['itemid']) && !empty($_POST['timestamp']))
			{
				// pass temporary image directory
				$tmp_image_dir = IM_IMAGE_UPLOAD_DIR.'tmp_'.(int)$_POST['timestamp'].'_'.$categoryid.'/';
				$fieldinput = $tmp_image_dir;
			} else
			{
				// pass image directory
				$fieldinput = IM_IMAGE_UPLOAD_DIR.$curitem->id.'.'.$categoryid.'/';
			}

			// position is send
			if(isset($_POST['position']) && is_array($_POST['position']))
			{
				$InputType->positions = $_POST['position'];
				$InputType->titles = isset($_POST['title']) ? $_POST['title'] : '';

				if(!file_exists($fieldinput.'config.xml'))
				{
					$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><params></params>');
					$i = 0;
					foreach($InputType->positions as $filepos => $filename)
					{
						$xml->image[$i]->name = $filename;
						$xml->image[$i]->position = $filepos;
						$xml->image[$i]->title = !empty($InputType->titles[$filepos])
							? $InputType->titles[$filepos] : '';
						$i++;
					}

				} else
				{
					$xml = simplexml_load_file($fieldinput.'config.xml');
					unset($xml->image);
					$i = 0;
					foreach($InputType->positions as $filepos => $filename)
					{
						$xml->image[$i]->name = $filename;
						$xml->image[$i]->position = $filepos;
						$xml->image[$i]->title = !empty($InputType->titles[$filepos])
							? $InputType->titles[$filepos] : '';
						$i++;
					}
				}
				if(is_dir($fieldinput)) $xml->asXml($fieldinput.'config.xml');
			}
		}*/













		/*if(!file_exists($value)) return $this->values;

		$temp_arr = array();


		if(empty($this->positions) && file_exists($value.'config.xml'))
		{
			$xml = simplexml_load_file($value.'config.xml');
			for($i = 0; $i < count($xml->image); $i++)
			{
				$this->positions[(int) $xml->image[$i]->position] = (string) $xml->image[$i]->name;
				$this->titles[(int) $xml->image[$i]->position] = (string) $xml->image[$i]->title;
			}
		}
		$i = 0;
		foreach(glob($value.'*') as $file)
		{
			if(is_dir($file) || 'xml' == pathinfo($file, PATHINFO_EXTENSION)) continue;

			$base = basename($file);
			$basedir = basename($value);

			$poskey = $i;
			$title = '';
			if(!empty($this->positions))
			{
				$poskey = array_search($base, $this->positions);
				$title = $this->titles[$poskey];
			}

			$temp_arr[$i] = new stdClass();
			$temp_arr[$i]->file_name = $base;
			$temp_arr[$i]->position = (int) $poskey;
			$temp_arr[$i]->path = $value;
			$temp_arr[$i]->fullpath = $value.$base;
			$temp_arr[$i]->url = 'data/uploads/imanager/'.$basedir.'/';
			$temp_arr[$i]->fullurl = 'data/uploads/imanager/'.$basedir.'/'.$base;
			$temp_arr[$i]->title = $title;

			$i++;
		}

		usort($temp_arr, array($this, 'sortObjects'));

		$this->values->value = $value;

		foreach($temp_arr as $key => $val)
		{
			$this->values->file_name[] = $temp_arr[$key]->file_name;
			$this->values->path[] = $temp_arr[$key]->path;
			$this->values->fullpath[] = $temp_arr[$key]->fullpath;
			$this->values->url[] = $temp_arr[$key]->url;
			$this->values->fullurl[] = $temp_arr[$key]->fullurl;
			$this->values->title[] = $temp_arr[$key]->title;
		}
		// delete empty config file
		if($i <= 0 && file_exists($value.'config.xml')) {unlink($value.'config.xml');}

		return $this->values;*/
	}


	public function prepareOutput(){return $this->values;}


	protected function sanitize($value){return imanager('sanitizer')->text($value);}


	private function sortObjects($a, $b)
	{
		$a = $a->position;
		$b = $b->position;

		if($a == $b) {return 0;}
		else
		{
			if($b > $a) {return -1;}
			else {return 1;}
		}
	}

	protected function getFullUrl()
	{
		return IM_SITE_URL;
	}
}