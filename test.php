<?php
require_once('VAR/VAR.php');


$search_address = "元智大學";
$url = "https://maps.googleapis.com/maps/api/geocode/json?address=$search_address&key=".googleAPIKey;
$response_json = json_decode(file_get_contents($url));

$req = $response_json->{'results'}[0]->{'geometry'}->{'location'};
// print $response_json->{'result'};
echo $req->{'lng'};
?>