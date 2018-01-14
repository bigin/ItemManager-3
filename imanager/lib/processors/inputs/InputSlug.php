<?php namespace Imanager;

class InputSlug implements InputInterface
{
	public $value;

	protected $field;

	const EMPTY_REQUIRED = -1;

	const ERR_MIN_LENGTH = -2;

	const ERR_MAX_LENGTH = -3;

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->value = '';
	}

	public function prepareInput($value, $sanitize = false)
	{
		$this->value = ($sanitize) ? $this->sanitize($value) : $value;

		// check input required
		if($this->field->required && empty($this->value)) {
			return self::EMPTY_REQUIRED;
		}
		// check min value
		if(!empty($this->field->minimum) && mb_strlen($this->value, 'UTF-8') < (int) $this->field->minimum) {
			return self::ERR_MIN_VALUE;
		}
		// check input max value
		if(!empty($this->field->maximum) && mb_strlen($this->value, 'UTF-8') > (int) $this->field->maximum) {
			return self::ERR_MAX_LENGTH;
		}

		return true;
	}

	public function prepareOutput($sanitize = false){
		return ($sanitize) ? $this->sanitize($this->value) : $this->value;
	}

	protected function sanitize($value){return imanager('sanitizer')->pageName($value);}
}