<?php
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$_id = $_GET['id'];
$restaurant_query = "SELECT name, review_star,food_star,speed_star,price_star, Introduction, address, phone_number, main_area, hours,lat,lng,food_type, main_pic, other_pic_1, other_pic_2, owner_id FROM restaurants WHERE _id='$_id' LIMIT 1";
$query_result = mysqli_query($db, $restaurant_query);
$restaurant = $query_result->fetch_assoc();
$comments_query = "SELECT count(*) as total FROM comments WHERE restaurant_id='$_id'";

$result=mysqli_query($db,$comments_query);
$total=$result->fetch_assoc();
$restaurant['comments_count']= $total['total'];

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
    "data"=>$restaurant,
];
send_response($response,200);

?>