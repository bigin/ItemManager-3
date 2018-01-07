<?php namespace Imanager;

class Manager
{
	protected static $categoryMapper = null;
	protected static $itemMapper = null;
	protected static $fieldMapper = null;
	protected static $templateEngine = null;
	protected static $sectionCache = null;
	protected $actionsProcessor = null;

	public $sanitizer = null;
	// Configuration Class
	public $config;

	// is ItemManager installed
	public static $installed;

	public $admin = null;

	public function __construct()
	{
		$this->config = Util::buildConfig();
		$this->sanitizer = new Sanitizer();
		Util::buildLanguage();
		$this->setActions();
	}


	public function setAdmin($admin)
	{
		$this->admin = $admin;
	}


	// Set Actions
	public function setActions()
	{
		global $plugins;
		$actions = array('ImActivated');
		if(function_exists('exec_action')) exec_action('ImActivated');
	}



	public function renameTmpDir($item)
	{
		$err = false;
		foreach($item->fields as $fieldname => $fieldvalue)
		{
			if($fieldvalue->type != 'imageupload' && $fieldvalue->type != 'fileupload')
				continue;

			$inputClassName = 'Input'.ucfirst($fieldvalue->type);
			$InputType = new $inputClassName($item->fields->$fieldname);


			// try to rename file directory
			$newpath = IM_IMAGE_UPLOAD_DIR.$item->id.'.'.$item->categoryid.'/';
			if(!rename($fieldvalue->value, $newpath))
				return false;

			$resultinput = $InputType->prepareInput($newpath);

			if(!isset($resultinput) || empty($resultinput))
				return false;

			foreach($resultinput as $inputputkey => $inputvalue)
				$item->fields->$fieldname->$inputputkey = $inputvalue;
		}

		if($item->save() && !$err) return true;

		return false;
	}


	/**
	 * Delete chached image files that starting with *_filename.* for example
	 *
	 * @param Item $item
	 */
	public function cleanUpCachedFiles(Item $item)
	{
		$fieldinput = IM_IMAGE_UPLOAD_DIR.(int)$item->id.'.'.$item->categoryid.'/';
		if(!file_exists($fieldinput.'config.xml')) {return;}
		$xml = simplexml_load_file($fieldinput.'config.xml');

		foreach(glob($fieldinput.'thumbnail/*_*.*') as $image) {
			$parts = explode('_', basename($image), 2);
			if(empty($parts[1])) continue;
			$chached = false;
			foreach($xml->image as $xmlimage)
			{
				if((string)$xmlimage->name == $parts[1]) {
					$chached = true;
					break;
				}
			}
			if($chached === true) { @unlink($image);}
		}
	}


	public function cleanUpTempContainers($datatyp)
	{
		if($datatyp == 'imageupload' || $datatyp == 'fileupload')
		{
			if(!file_exists(IM_IMAGE_UPLOAD_DIR))
				return false;

			foreach(glob(IM_IMAGE_UPLOAD_DIR.'tmp_*_*') as $file)
			{
				$base = basename($file);
				$strp = explode('_', $base);

				// wrong file name, continue
				if(count($strp) < 3)
					continue;

				if(!$this->cp->isCategoryValid($strp[2]))
					$this->delTree($file);

				$min_days = intval($this->config->backend->min_tmpimage_days);
				$storagetime =  time() - (60 * 60 * 24 * $min_days);

				if($strp[1] < $storagetime && $storagetime > 0)
					$this->delTree($file);
			}
			return true;
		}
	}


	protected function delTree($dir)
	{
		if(!file_exists($dir))
			return false;
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file)
		{
			(is_dir("$dir/$file") && !is_link($dir)) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}