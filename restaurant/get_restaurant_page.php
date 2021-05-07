<?php
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$_id = $_GET['id'];
$restaurant_query = "SELECT name, review_star, Introduction, address, phone_number, main_area, hours, main_pic, other_pic_1, other_pic_2, owner_id FROM restaurants WHERE _id='$_id' LIMIT 1";
$query_result = mysqli_query($db, $restaurant_query);
$restaurant = $query_result->fetch_assoc();
if(!$restaurant){
    $response = [
        "ok"=>false,
        "data"=> new stdClass()
    ];
    send_response($response,203);
}
$restaurant['review_star'] = floatval($restaurant['review_star']);
$response = [
    "ok"=>true,
    "data"=>$restaurant
];
send_response($response,200);

?>