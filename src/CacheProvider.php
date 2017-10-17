<?php

class CacheProvider implements CacheProviderInterface {
	protected $pool;

	function __construct($cachePath = "cache/") {
		$driver = new \Stash\Driver\FileSystem([
			"path" => $cachePath
		]);

		$this->pool = new \Stash\Pool($driver);
	}

	protected static function getCachePath(string $endpoint, array $params) {
		unset($params[ChggAPI::API_KEY]);
		return $endpoint . "/" . http_build_query($params);
	}

	public function set(string $endpoint, array $params, $data) {

	}

	public function get(string $endpoint, array $params) {

	}
}

?>
