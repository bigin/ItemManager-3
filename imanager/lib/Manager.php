<?php namespace Imanager;

class Manager
{
	/**
	 * @var Sanitizer|null - Sanitizer instance
	 */
	public $sanitizer = null;

	/**
	 * @var Config|null - Configuration class instance
	 */
	public $config = null;


	//public $admin = null;

	/**
	 * @var Input|null - Input class instance
	 */
	public $input = null;

	/**
	 * @since v 3.0
	 * Manager constructor.
	 */
	public function __construct()
	{
		spl_autoload_register(array($this, 'loader'));

		require_once(IM_SOURCEPATH.'processors/FieldInterface.php');
		require_once(IM_SOURCEPATH.'processors/InputInterface.php');

		$this->config = Util::buildConfig();
		$this->sanitizer = new Sanitizer();
		$this->input = new Input($this->config, $this->sanitizer);
		Util::buildLanguage();
		$this->setActions();
		set_error_handler(__NAMESPACE__.'\Util::imErrorHandler');
	}

	/**
	 * @return null
	 */
	public function __get($name)
	{
		if(!isset($this->$name)) {
			$funcName = '_im' . ucfirst($name);
			if(method_exists($this, $funcName)) {
				$this->$name = $this->$funcName();
				return $this->$name;
			}
			return null;
		} else {
			return $this->$name;
		}
	}

	/**
	 * Autoload method
	 *
	 * @since v 3.0
	 * @param $lclass - Class pattern
	 */
	private function loader($lclass)
	{
		$classPattern = str_replace(__NAMESPACE__.'\\', '', $lclass);
		$classPath = IM_SOURCEPATH . $classPattern . '.php';
		$fieldsPath = IM_SOURCEPATH . 'processors/fields/' . $classPattern. '.php';
		$inputsPath = IM_SOURCEPATH . 'processors/inputs/' . $classPattern . '.php';
		if(file_exists($classPath)) include($classPath);
		elseif(file_exists($fieldsPath)) include($fieldsPath);
		elseif(file_exists($inputsPath)) include($inputsPath);
	}

	/**
	 * Auto-Callable
	 *
	 * @since v 3.0
	 * @return CategoryMapper
	 */
	protected function _imCategoryMapper() { return new CategoryMapper(); }

	/**
	 * Auto-Callable
	 *
	 * @since v 3.0
	 * @return FieldMapper
	 */
	protected function _imFieldMapper() { return new FieldMapper(); }

	/**
	 * Auto-Callable
	 *
	 * @since v 3.0
	 * @return ItemMapper
	 */
	protected function _imItemMapper() { return new ItemMapper(); }


	/**
	 * Auto-Callable
	 *
	 * @since v 3.0
	 * @return TemplateParser
	 */
	protected function _imTemplateParser()
	{
		$this->templateParser = new TemplateParser();
		$this->templateParser->init();
		return $this->templateParser;
	}

	/**
	 * Auto-Callable
	 *
	 * @since v 3.0
	 * @return SectionCache
	 */
	protected function _imSectionCache() { return new SectionCache(); }






	// Todo: check is used in 3.0?
	public function setAdmin($admin)
	{
		$this->admin = $admin;
	}

	// Todo: check is used in 3.0?
	// Set Actions
	public function setActions()
	{
		//global $plugins;
		//$actions = array('imstart');
		if(function_exists('exec_action')) exec_action('imstart');
	}

	// Todo: check is used in 3.0?
	public function renameTmpDir($item)
	{
		$err = false;
		foreach($item->fields as $fieldname => $fieldvalue)
		{
			if($fieldvalue->type != 'imageupload' && $fieldvalue->type != 'fileupload') continue;

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

	// Todo: check is used in 3.0?
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

	// Todo: check is used in 3.0?
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
}