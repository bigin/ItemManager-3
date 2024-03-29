<?php namespace Imanager;

class Input
{
	public $sanitizer;
	public $requestBody;
	public $post;
	public $get;
	public $put;
	public $patch;
	public $pageNumber = 0;
	public $urlSegments;

	protected $config;

	public function __construct($config, $sanitizer) {
		$this->sanitizer = $sanitizer;
		$this->config = $config;
		$this->requestBody = file_get_contents('php://input'); 
		$this->urlSegments = new UrlSegments($this->sanitizer);
		$this->parseUrl();
		$this->post = new Post();
		$this->get = new Get();
		$this->patch = new Patch();
		$this->put = new Put();
		$this->whitelist = new Whitelist();
		$this->buildSubmitedData();
	}

	private function parseUrl() {
		$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$siteRootUrl = $this->config->getUrl();
		if(empty($currentUrl)) return;
		$pathOnly = str_replace($siteRootUrl, '', $currentUrl);
		$this->buildSegments($pathOnly);
	}

	/*
	 * foreach(array_values(array_filter(array_map('trim', explode('/', $parseUrl['path'])), function($value) {
	 *				return ($value !== null && $value !== false && $value !== ''); 
	 *		})) as $key => $value) { ...
	 */
	private function buildSegments($url) {
		$parseUrl = parse_url(trim($url));
		if(isset($parseUrl['path'])) {
			foreach(array_values(array_filter(array_map('trim', explode('/', $parseUrl['path'])), 'strlen')) as $key => $value) {
				$this->urlSegments->set($key, $value);
				$this->urlSegments->total++;
			}
		}
		// /* (false !== strpos($this->urlSegments->getLast(), $this->config->pageNumbersUrlSegment)) */ 
		if($this->urlSegments->total && preg_match('/'.$this->config->pageNumbersUrlSegment.'(\d+)/i', $this->urlSegments->getLast())) {
			$this->pageNumber = (int) str_replace($this->config->pageNumbersUrlSegment, '', $this->urlSegments->getLast());
			$this->urlSegments->total--;
			unset($this->urlSegments->segment[$this->urlSegments->total]);
		}
	}

	private function getHost($address) {
		$parseUrl = parse_url(trim($address));
		return trim($parseUrl['host'] ? $parseUrl['host'] :
			array_shift(explode('/', $parseUrl['path'], 2)));
	}

	private function buildSubmitedData() {
		foreach($_POST as $key => $value) { $this->post->{$key} = $value; }
		foreach($_GET as $key => $value) { $this->get->{$key} = $value; }
		if($_SERVER['REQUEST_METHOD'] == 'PATCH') {
			parse_str($this->requestBody, $_PATCH);
			foreach($_PATCH as $key => $value) { $this->patch->{$key} = $value; }
		}
		elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {
			parse_str($this->requestBody, $_PUT);
			foreach($_PUT as $key => $value) { $this->put->{$key} = $value; }
		}
		//foreach($_PATCH as $key => $value) { $this->post->{$key} = $value; }
		if(!$this->pageNumber && isset($_GET[$this->config->pageNumbersUrlSegment]) &&
			(int) $_GET[$this->config->pageNumbersUrlSegment] != 0) {
			$this->pageNumber = (int) $_GET[$this->config->pageNumbersUrlSegment];
		}
	}

}

class UrlSegments
{
	public $total = 0;

	public function __construct($sanitizer) { $this->sanitizer = $sanitizer; }

	public function set($id, $value) {
		$this->segment[$id] = $this->sanitizer->path($value);
	}

	public function get($id) {
		return isset($this->segment[$id]) ? $this->segment[$id] : null;
	}

	public function getLast() {
		return isset($this->segment[($this->total - 1)]) ? $this->segment[($this->total - 1)] : null;
	}

	public function getUrl($options = [])
	{
		$defaults = [
			'useTrailingSlash' => true
		];
		$options = array_merge($defaults, $options);
		if($this->total <= 1) {
			if($this->total == 0) { return '';}
			return $this->segment[($this->total - 1)].(($options['useTrailingSlash']) ? '/' : '');
		}
		$buf = '';
		foreach($this->segment as $key => $value) {
			$buf .= $this->segment[($key)].'/';
		}
		return (($options['useTrailingSlash']) ? $buf : substr($buf, 0, -1));
	}
}

class Post
{
	/**
	 * Provides direct reference access to set values in the $data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * return $this
	 *
	 */
	public function __set($key, $value) { $this->{$key} = $value;}
	public function __get($name) { return isset($this->{$name}) ? $this->{$name} : null;}
}

class Get
{
	/**
	 * Provides direct reference access to set values in the $data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * return $this
	 *
	 */
	public function __set($key, $value) { $this->{$key} = $value; }
	public function __get($name) { return isset($this->{$name}) ? $this->{$name} : null; }
}

class Patch
{
	/**
	 * Provides direct reference access to set values in the $data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * return $this
	 *
	 */
	public function __set($key, $value) { $this->{$key} = $value; }
	public function __get($name) { return isset($this->{$name}) ? $this->{$name} : null; }
}

class Put
{
	/**
	 * Provides direct reference access to set values in the $data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * return $this
	 *
	 */
	public function __set($key, $value) { $this->{$key} = $value; }
	public function __get($name) { return isset($this->{$name}) ? $this->{$name} : null; }
}

class Whitelist
{
	/**
	 * Provides direct reference access to set values in the $data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * return $this
	 *
	 */
	public function __set($key, $value) { $this->{$key} = $value; }
	public function __get($name) { return isset($this->{$name}) ? $this->{$name} : null; }
}
