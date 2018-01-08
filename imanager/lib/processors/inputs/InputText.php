<?php namespace Imanager;

class InputText implements InputInterface
{
	public $value;

	protected $field;

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
			MsgReporter::setError('err_empty_required_field_value', array('name' => $this->field->name));
			return false;
		}
		// check min value length
		if(!empty($this->field->minimum) && mb_strlen($this->value, 'UTF-8') < (int) $this->field->minimum) {
			MsgReporter::setError('err_min_length_field_value', array(
				'name' => $this->field->name,
				'length' => $this->field->minimum)
			);
			return false;
		}
		// check input max value
		if(!empty($this->field->maximum) && mb_strlen($this->values->value, 'UTF-8') > (int) $this->field->maximum) {
			MsgReporter::setError('err_max_length_field_value', array(
					'name' => $this->field->name,
					'length' => $this->field->maximum)
			);
			return false;
		}

		return true;
	}

	public function prepareOutput($sanitize = false) {
		return ($sanitize) ? $this->sanitize($this->value) : $this->value;
	}

	protected function sanitize($value) { return imanager('sanitizer')->text($value); }
}