<?php
namespace ChggAPI;

use \Psr\Cache\CacheItemInterface;

class Cache {
	protected $pool;

	protected $cacheDuration = [
		ChggAPI::CHAMPIONS_ENDPOINT => 86400,
		ChggAPI::MATCHUP_ENDPOINT => 86400,
		ChggAPI::GENERAL_ENDPOINT => 86400,
		ChggAPI::OVERALL_ENDPOINT => 86400
	];

	public function __construct() {
		$this->pool = new \Stash\Pool();
	}

	public function getItem(string $endpoint,
							array $parameters) : CacheItemInterface {
		$cachePath = self::getCachePath($endpoint, $parameters);

		return $this->pool->getItem($cachePath);
	}

	public function save(\Psr\Cache\CacheItemInterface $item) {
		$duration = array_filter($this->cacheDuration, function($key) use ($item) {
			return substr($item->getKey(), 0, strlen($key)) === $key;
		}, ARRAY_FILTER_USE_KEY);
		$duration = reset($duration);

		if ($duration) {
			$item->expiresAfter($duration);
		}

		$this->pool->save($item);
	}

	protected static function getCachePath(string $endpoint,
										   array $params = []) : string {
		unset($params[ChggAPI::API_KEY]);
		return $endpoint . "&" . http_build_query($params);
	}

	public function getPool() : \Psr\Cache\CacheItemPoolInterface {
		return $this->pool;
	}
}

?>
