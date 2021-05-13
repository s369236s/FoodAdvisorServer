<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$_id = $_GET['id'];
$comments_query = "SELECT user_id FROM comments WHERE restaurant_id='$_id' LIMIT 10";
$query_result = mysqli_query($db, $comments_query);
$data = [];
while($row = mysqli_fetch_array($query_result, MYSQLI_ASSOC))
{
    array_push($data,$row);
}
if(!$data){
    $response = [
        "ok"=>false,
        "data"=>$data,
        "errors"=>"something wrong"
    ]; 
send_response($response,203);
}
$response = [
    "ok"=>true,
    "data"=>$data
];
send_response($response,200);?>