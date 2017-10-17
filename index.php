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

echo "Overall:<br>";
printr($chggAPI->getOverall());

echo "General:<br>";
printr($chggAPI->getGeneral());

echo "Champion:<br>";
printr($chggAPI->getChampions(1));

echo "Matchups:<br>";
printr($chggAPI->getMatchups(42));

echo "Matchups with role:<br>";
printr($chggAPI->getMatchups(36, "MIDDLE"));

echo "All champions:<br>";
printr($chggAPI->getChampions());

?>
