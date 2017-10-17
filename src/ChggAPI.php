<?php

namespace ChggAPI;

use GuzzleHttp\Client;

class ChggAPI {
	public const
		CHAMPIONS_ENDPOINT = "champions",
		MATCHUP_ENDPOINT = "matchup",
		GENERAL_ENDPOINT = "general",
		OVERALL_ENDPOINT = "overall",

		CHAMPIONS_ID = "id",
		CHAMPIONS_LIMIT = "limit",
		CHAMPIONS_SKIP = "skip",
		CHAMPIONS_ELO = "elo",
		CHAMPIONS_CHAMP_DATA = "champData",
		CHAMPIONS_SORT = "sort",
		CHAMPIONS_ABRIDGED = "abridged",
		CHAMPIONS_PARAMETERS = [self::CHAMPIONS_LIMIT, self::CHAMPIONS_SKIP,
			self::CHAMPIONS_ELO, self::CHAMPIONS_CHAMP_DATA,
			self::CHAMPIONS_SORT, self::CHAMPIONS_ABRIDGED],

		MATCHUP_ID = "id",
		MATCHUP_ELO = "elo",
		MATCHUP_SKIP = "skip",
		MATCHUP_LIMIT = "limit",
		MATCHUP_ROLE = "role",
		MATCHUP_PARAMETERS = [self::MATCHUP_ELO, self::MATCHUP_SKIP, self::MATCHUP_LIMIT],

		GENERAL_ELO = "elo",
		GENERAL_PARAMETERS = [self::GENERAL_ELO],

		OVERALL_ELO = "elo",
		OVERALL_PARAMETERS = [self::OVERALL_ELO],

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

	public function getChampions(int $id = null, array $params = []) {
		$query = $this->prepareQuery(self::CHAMPIONS_PARAMETERS, $params);
		$cacheQuery = $query;

		$url = "champions";

		if ($id !== null) {
			$url .= "/".$id;
			$cacheQuery[self::CHAMPIONS_ID] = $id;
		}

		$item = $this->cache->getItem(self::CHAMPIONS_ENDPOINT, $cacheQuery);

		if ($item->isHit()) {
			$data = $item->get();
		} else {
			$item->lock();

			$response = $this->client->get($url, [
				"query" => $query
			]);

			$data = json_decode($response->getBody());
			$item->set($data);
			$this->cache->save($item);
		}

		return $data;
	}

	public function getMatchups(int $id, string $role = null, array $params = []) {
		$query = $this->prepareQuery(self::MATCHUP_PARAMETERS, $params);

		$cacheQuery = $query;
		$cacheQuery[self::MATCHUP_ID] = $id;

		if ($role != null) {
			$cacheQuery[self::MATCHUP_ROLE] = $role;
		}

		$item = $this->cache->getItem(self::MATCHUP_ENDPOINT, $cacheQuery);

		if ($item->isHit()) {
			$data = $item->get();
		} else {
			$item->lock();

			$url = "champions/".$id."/";

			if ($role !== null) {
				$url .= $role . "/";
			}

			$url .= "matchups";

			$response = $this->client->get($url, [
				"query" => $query
			]);

			$data = json_decode($response->getBody());
			$item->set($data);
			$this->cache->save($item);
		}

		return $data;
	}

	public function getGeneral(array $params = []) {
		$query = $this->prepareQuery(self::GENERAL_PARAMETERS, $params);

		$item = $this->cache->getItem(self::GENERAL_ENDPOINT, $query);

		if ($item->isHit()) {
			$data = $item->get();
		} else {
			$item->lock();

			$response = $this->client->get("general", [
				"query" => $query
			]);

			$data = json_decode($response->getBody());
			$item->set($data);
			$this->cache->save($item);
		}

		return $data;
	}

	public function getOverall(array $params = []) {
		$query = $this->prepareQuery(self::OVERALL_PARAMETERS, $params);

		$item = $this->cache->getItem(self::OVERALL_ENDPOINT, $query);

		if ($item->isHit()) {
			$data = $item->get();
		} else {
			$item->lock();

			$response = $this->client->get("overall", [
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
