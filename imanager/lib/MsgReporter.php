<?php namespace Imanager;

class MsgReporter
{
	public static $dir = 'imanager';
	private static $_msgs = array();
	private static $_error = false;

	public static function setMessage($name, array $var=array())
	{
		global $i18n;
		$msgs = new Message();
		$msgs->type = 'note';

		if(!isset($i18n[$name])) { $msgs->text = '';
		} else {
			$msgs->text = $i18n[$name];

			if($var) {
				foreach($var as $key => $value) {
					$msgs->text = preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $msgs->text);
				}
			}
		}

		self::$_msgs[] = $msgs;
	}

	public static function getMessages() { if(self::$_msgs) return self::$_msgs; }

	public static function setError($name='err_general', array $var=array())
	{
		global $i18n;
		self::$_error = true;
		$msgs = new Message();
		$msgs->type = 'error';

		if(!isset($i18n[$name])) { $msgs->text = '';
		} else {
			$msgs->text = $i18n[$name];

			if($var) {
				foreach($var as $key => $value) {
					$msgs->text = preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $msgs->text);
				}
			}
		}

		self::$_msgs[] = $msgs;
	}

	public static function isError(){return self::$_error;}

}

class Message
{
	public $type = null;
	public $text = null;

	public static function render()
	{
		if($type == 'error') return '<li class="error msg">'.$text.'</li>';
		elseif($type == 'note') return '<li class="note msg">'.$text.'</li>';
	}
}

?>
