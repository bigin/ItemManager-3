<?php namespace Imanager;

class InputPassword implements InputInterface
{
	protected $value;

	protected $field;

	const EMPTY_REQUIRED = -1;

	const ERR_MIN_LENGTH = -2;

	const ERR_MAX_LENGTH = -3;

	const WRONG_VALUE_FORMAT = -4;

	const COMPARISON_FAILED = -5;

	public $errorCode = null;

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->value = new PasswordFieldValue();
	}

	public function prepareInput($value, $sanitize = false)
	{
		if(!is_array($value)) {
			$this->errorCode = self::WRONG_VALUE_FORMAT;
			return false;
		}

		if(!isset($value['password']) || !isset($value['confirm_password'])) {
			$this->errorCode = self::EMPTY_REQUIRED;
			return false;
		}

		$password = trim($value['password']);
		$confirm_password = trim($value['confirm_password']);

		// Compare pass and confirmation pass
		if($password != $confirm_password) {
			$this->errorCode = self::COMPARISON_FAILED;
			return false;
		}

		// check min value
		if(!empty($this->field->minimum) && mb_strlen($password) < intval($this->field->minimum)) {
			$this->errorCode = self::ERR_MIN_LENGTH;
			return false;
		}
		// check input max value
		if(!empty($this->field->maximum) && mb_strlen($password) > intval($this->field->maximum)) {
			$this->errorCode = self::ERR_MAX_LENGTH;
			return false;
		}

		$this->value->salt = $this->randomString();
		$this->value->password = sha1($password.$this->value->salt);

		return true;
	}

	public function prepareOutput(){ return $this->value; }

	public function randomString($length = 10)
	{
		$characters = '0123456*789abcdefg$hijk#lmnopqrstuvwxyzABC+EFGHIJKLMNOPQRSTUVW@XYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i = 0; $i < $length; $i++)
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
	}
}
