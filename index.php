<?php

require __DIR__ . "/vendor/autoload.php";

use GuzzleHttp\Client;
use ChggAPI\ChggAPI;

$config = parse_ini_file(__DIR__."/../../config.ini", true);

$chggAPI = new ChggAPI($config["chgg-api"]["key"]);
$chggAPI->getCache()->getPool()->setDriver(new \Stash\Driver\FileSystem([
	"path" => "cache/"
]));

echo "Overall:<br><pre>";
print_r($chggAPI->getOverall());

echo "</pre>General:<br><pre>";
print_r($chggAPI->getGeneral());

echo "</pre>Champion:<br><pre>";
print_r($chggAPI->getChampions(1));

echo "</pre>Matchups:<br><pre>";
print_r($chggAPI->getMatchups(42));

echo "Matchups with role:<br><pre>";
print_r($chggAPI->getMatchups(36, "MIDDLE"));

echo "</pre>All champions:<br><pre>";
print_r($chggAPI->getChampions());
echo "</pre>";
