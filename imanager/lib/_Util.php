<?php namespace Imanager;

class Util
{
	/**
	 * Build ItemManager configuration
	 *
	 * @return Config object
	 */
	public static function buildConfig()
	{
		$config = new Config();
		include(IM_ROOTPATH.'imanager/inc/config.php');
		if(file_exists(IM_ROOTPATH.'imanager/inc/custom.config.php')) { include(IM_ROOTPATH.'imanager/inc/custom.config.php'); }
		return $config;
	}

	/**
	 * @param null $path
	 * @param string $language
	 */
	public static function buildLanguage($path=null, $language='en_US.php')
	{
		global $i18n;
		if(file_exists(IM_ROOTPATH.'imanager/lang/'.$language)) { include(IM_ROOTPATH.'imanager/lang/'.$language); }
	}

	public static function dataLog($data, $file = '')
	{
		$filename = empty($file) ? GSDATAOTHERPATH.'logs/imlog_'.date('Ym').'.txt' : GSDATAOTHERPATH.'logs/'.$file.'.txt';
		if (!$handle = fopen($filename, 'a+'))
		{
			return;
		}
		$datum = date('d.m.Y - H:i:s', time());
		if (!fwrite($handle, '[ '.$datum.' ]'. ' ' . print_r($data, true) . "\r\n")) {
			return;
		}
		fclose($handle);
	}

	public static function preformat($data){echo '<pre>'.print_r($data, true).'</pre>';}


	public static function install($path) {
		if(!mkdir(dirname($path), imanager('config')->chmodDir, true)) echo 'Unable to create path: '.dirname($path);
	}

	// TODO: Wird diese hier benutzt?
	public static function isTimestamp($string){return (1 === preg_match( '~^[1-9][0-9]*$~', $string ));}

	/**
	 * Check the PHP_INT_SIZE constant. It'll vary based on the size of the register (i.e. 32-bit vs 64-bit)
	 * In 32-bit systems PHP_INT_SIZE should be 4, for 64-bit it should be 8
	 *
	 * @return int
	 */
	public static function getIntSize() {return PHP_INT_SIZE;}

	/**
	 * Function to compute the unsigned crc32 value.
	 * PHP crc32 function returns int which is signed, so in order to get the correct crc32 value
	 * we need to convert it to unsigned value.
	 *
	 * NOTE: it produces different results on 64-bit compared to 32-bit PHP system
	 *
	 * @param $str - String to compute the unsigned crc32 value.
	 * @return $var - Unsinged inter value.
	 */
	public static function computeUnsignedCRC32($str)
	{
		sscanf(crc32($str), "%u", $var);
		return $var;
	}


	public static function redirect($url, $flag = true, $statusCode = 303)
	{
		header('Location: ' . htmlspecialchars($url), $flag, $statusCode);
		die();
	}

	public static function exec_action($a)
	{
		global $plugins;

		foreach ($plugins as $hook) {
			if ($hook['hook'] == $a) {
				call_user_func_array($hook['function'], $hook['args']);
			}
		}
	}


	public static function createBackup($path, $file, $suffix)
	{
		if(!file_exists($path.$file.$suffix)) return false;
		$stamp = time();
		if(!copy($path.$file.$suffix, IM_BACKUPPATH.'backup_'.$stamp.'_'.$file.$suffix)) return false;
		self::deleteOutdatedBackups($suffix);
		return true;
	}


	public static function deleteOutdatedBackups($suffix)
	{
		switch ($suffix)
		{
			case IM_FIELDS_SUFFIX:
				$token = 'field';
				break;
			case IM_CATEGORY_SUFFIX:
				$token = 'cat';
				break;
			case IM_ITEM_SUFFIX:
				$token = 'item';
				break;
			default:
				return false;
		}
		$min_days = (int) imanager('config')->minBackupTimePeriod;
		foreach(glob(IM_BACKUPPATH.'backup_*_*'.$suffix) as $file) {
			if(self::isCacheFileExpired($file, $min_days)) { self::removeFilename($file);}
		}
	}

	/**
	 * Is the given backup filename expired?
	 *
	 * @param string $filename
	 * @return bool
	 *
	 */
	protected static function isCacheFileExpired($filename, $min_days)
	{
		if(!$mtime = @filemtime($filename)) return false;
		if(($mtime + (60 * 60 * 24 * $min_days)) < time()) {
			return true;
		}
		return false;
	}


	/**
	 * Removes just the given file
	 */
	protected static function removeFilename($filename){@unlink($filename);}
}