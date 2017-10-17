<?php

interface CacheProviderInterface {
	public function set(string $endpoint, array $params);
	public function get(string $endpoint, array $params);
}

?>
