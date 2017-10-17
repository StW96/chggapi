<?php

require __DIR__ . "/vendor/autoload.php";

use GuzzleHttp\Client;
use ChggAPI\ChggAPI;

function printr($value, $return = false) {
	echo "<pre>";
	$output = print_r($value);
	echo "</pre>";

	if ($return) {
		return $output;
	}
}

$chggAPI = new ChggAPI("7ec89ef90cd2ed610d12b6be2644519e");
$chggAPI->getCache()->getPool()->setDriver(new \Stash\Driver\FileSystem([
	"path" => "cache/"
]));

printr($chggAPI->getChampions());

?>
