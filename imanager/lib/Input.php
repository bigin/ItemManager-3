<?php

class Input
{
	public $sanitizer;
	public $urlSegments;

	protected $config;

	public function __construct($config) {
		$this->sanitizer = new \Sanitizer();
		$this->config = $config;
		$this->urlSegments = new UrlSegments($this->sanitizer);
		$this->parseUrl();
	}

	private function parseUrl() {
		$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$siteRootUrl = $this->config->getSiteUrl();
		if(empty($currentUrl)) return;
		$pathOnly = str_replace($siteRootUrl, '', $currentUrl);
		$this->buildSegments($pathOnly);
	}

	private function buildSegments($url) {
		$parseUrl = parse_url(trim($url));
		if(isset($parseUrl['path'])) {
			foreach(array_values(array_filter(array_map('trim', explode('/', $parseUrl['path'])))) as $key => $value) {
				$this->urlSegments->set($key, $value);
				$this->urlSegments->total++;
			}
		}
	}

	private function getHost($address) {
		$parseUrl = parse_url(trim($address));
		return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
	}

}

class UrlSegments
{
	public $total = 0;

	public function __construct($sanitizer) { $this->sanitizer = $sanitizer; }

	public function set($id, $value) {
		$this->segment{$id} = $this->sanitizer->path($value);
	}

	public function get($id) {
		return isset($this->segment{$id}) ? $this->segment{$id} : null;
	}

	public function getLast() {
		return isset($this->segment{($this->total - 1)}) ? $this->segment{($this->total - 1)} : null;
	}
}
