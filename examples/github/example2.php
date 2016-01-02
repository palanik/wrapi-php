<?php

require 'vendor/autoload.php';

require "src/wrapi.php";

$client = new wrapi\wrapi('https://api.github.com/');

$client("zen", array("method" => "GET", "path" => "zen"));

$resp = $client->zen();
echo "Today's Zen advice: '". $resp. "'\n\n";

?>
