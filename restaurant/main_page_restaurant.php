<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$query = "SELECT id,name,review_star,main_area FROM restaurants ORDER BY id LIMIT 4" ;
$result = mysqli_query($db, $query);
$data = [];
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
    array_push($data,$row);
}
if(!$data){
    $response = [
        "ok"=>false,
        "data"=>new stdClass(),
        "errors"=>"something wrong"
    ]; 
send_response($response,203);
}

$response = [
    "ok"=>true,
    "data"=>$data,
    "errors"=>""
];
send_response($response,200);
