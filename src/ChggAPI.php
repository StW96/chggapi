<?php

namespace ChggAPI;

use GuzzleHttp\Client;

class ChggAPI {
	public const
		CHAMPIONS_LIMIT = "limit",
		CHAMPIONS_SKIP = "skip",
		CHAMPIONS_ELO = "elo",
		CHAMPIONS_CHAMP_DATA = "champData",
		CHAMPIONS_SORT = "sort",
		CHAMPIONS_ABRIDGED = "abridged",

		API_KEY = "api_key";

	protected $key;
	protected $client;

	protected $pool;

	function __construct($key) {
		$this->key = $key;
		$this->client = new Client(["base_uri" => "api.champion.gg/v2/"]);

		$driver = new \Stash\Driver\FileSystem([
			"path" => "cache/"
		]);
		$this->pool = new \Stash\Pool($driver);
	}

	protected function prepareQuery(array $keys = [], array $params = []) {
		$query = array_fill_keys($keys, null);
		$query = array_intersect_key($params, $query);
		$query[self::API_KEY] = $this->key;

		return $query;
	}

	protected static function getCachePath(string $root, array $params = []) {
		unset($params[self::API_KEY]);
		return $root . "/" . http_build_query($params);
	}

	public function getChampions(array $params = []) {
		$cachePath = self::getCachePath("champions", $params);
		printf($cachePath);

		$item = $this->pool->getItem($cachePath);

		if ($item->isHit()) {
			echo "<br>Cached";
			$data = $item->get();
		} else {
			echo "<br>Not cached";
			$item->lock();

			$query = $this->prepareQuery(
				[self::CHAMPIONS_LIMIT, self::CHAMPIONS_SKIP,
				self::CHAMPIONS_ELO, self::CHAMPIONS_CHAMP_DATA,
				self::CHAMPIONS_SORT, self::CHAMPIONS_ABRIDGED],
				$params);


			$response = $this->client->get("champions", [
				"query" => $query
			]);

			$data = json_decode($response->getBody());

			$item->set($data);
			$item->expiresAfter(86400);

			$this->pool->save($item);
			echo "<br>".date("Y-m-d H:i:s", time())."<br>";
			echo $item->getExpiration()->format("Y-m-d H:i:s");
		}

		printr($data);
	}
}

?>
