<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);

$name_query = $_GET['name_query'];
$restaurant_query = "SELECT * FROM restaurants WHERE SOUNDEX(name)= SOUNDEX('$name_query') ORDER BY review_star LIMIT 5" ;
$query_result = mysqli_query($db, $restaurant_query);
$data = [];
while($row = mysqli_fetch_array($query_result, MYSQLI_ASSOC))
{
    array_push($data,$row);
}
if(!$data){
    $response = [
        "ok"=>false,
        "data"=>$data,
        "errors"=>$db->error
    ]; 
send_response($response,203);
}

$response = [
    "ok"=>true,
    "data"=>$data,
    "errors"=>""
];
send_response($response,200);

?>