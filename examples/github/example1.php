<?php

require 'vendor/autoload.php';

require "src/wrapi.php";

$endpoints = array(
    "zen" => array("method" => "GET", "path" => "zen"),
    "contributors" => array("method" => "GET", "path" => "repos/:owner/:repo/contributors"),
    "searchRepos" => array("method" => "GET", "path" => "search/repositories")
    );

$client = new wrapi\wrapi('https://api.github.com/', $endpoints);

$resp = $client->zen();
echo "Today's Zen advice: '". $resp. "'\n\n";


// Print all Contributors to guzzle repo.
$resp = $client->contributors('guzzle', 'guzzle');

echo "Contributions to guzzle/guzzle\n";
echo "------------------------------\n";
foreach ($resp as $contributor) {
    echo $contributor['login']. " made ". $contributor['contributions']. " contributions.\n";
}
echo "\n\n";

echo "Popular PHP repos this week on github.com\n";
echo "-----------------------------------------\n";

date_default_timezone_set("America/New_York");
$lastWeek = date('Y-m-d', strtotime("-1 week"));
$qs = array(
    "q" => 'created:>'. $lastWeek. ' language:php',
    "sort" => urlencode("stars"),
    "order" => urlencode("desc")
    );
$resp = $client->searchRepos($qs);
foreach ($resp["items"] as $item) {
    echo $item['full_name']. "\n";
    if (strlen($item['description']) > 0) {
        echo $item['description']. "\n";
    }
    echo "Stars: ". $item['stargazers_count']. "\n";
    echo "\n";
}
?>
