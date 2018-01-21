<?php namespace Imanager;

class Config
{
	public $siteUrl;
	/**
	 * Provides direct reference access to set values in the $data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * return $this
	 *
	 */
	public function __set($key, $value) {
		$this->{$key} = $value;
	}

	public function getSiteUrl() {
		return ($this->siteUrl) ? $this->siteUrl : $this->buildSiteUrl();
	}

	protected function buildSiteUrl() {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
		$path_parts = pathinfo(htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES));
		//$path_parts = str_replace('/manager', "", $path_parts['dirname']);
		$port = ($p = $_SERVER['SERVER_PORT']) != '80' && $p != '443' ? ':'.$p : '';
		$this->siteUrl = $protocol.htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES).$port.$path_parts['dirname'].'/';
		return $this->siteUrl;
	}


}
?>