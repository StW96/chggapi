<?php

namespace ChggAPI;

use GuzzleHttp\Client;

class ChggAPI {
	public const
		CHAMPIONS_ENDPOINT = "champions",
		MATCHUP_ENDPOINT = "matchup",
		GENERAL_ENDPOINT = "general",
		OVERALL_ENDPOINT = "overall",

		CHAMPIONS_LIMIT = "limit",
		CHAMPIONS_SKIP = "skip",
		CHAMPIONS_ELO = "elo",
		CHAMPIONS_CHAMP_DATA = "champData",
		CHAMPIONS_SORT = "sort",
		CHAMPIONS_ABRIDGED = "abridged",

		API_KEY = "api_key";

	protected $key;
	protected $client;

	protected $cache;

	function __construct($key) {
		$this->key = $key;
		$this->client = new Client(["base_uri" => "api.champion.gg/v2/"]);

		$this->cache = new Cache();
	}

	protected function prepareQuery(array $keys = [], array $params = []) : array {
		$query = array_fill_keys($keys, null);
		$query = array_intersect_key($params, $query);
		$query[self::API_KEY] = $this->key;

		return $query;
	}

	public function getChampions(array $params = []) {
		$item = $this->cache->getItem(SELF::CHAMPIONS_ENDPOINT, $params);

		if ($item->isHit()) {
			$data = $item->get();
		} else {
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
			$this->cache->save($item);
		}

		return $data;
	}

	public function getCache() : Cache {
		return $this->cache;
	}
}

?>
