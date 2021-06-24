<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$_id = $_GET['user_id'];
$user_query = "SELECT username,intro,pic,address,recent_comment_1,recent_comment_2,recent_comment_3 FROM users WHERE _id='$_id' LIMIT 1";
$query_result = mysqli_query($db, $user_query);
$user = $query_result->fetch_assoc();
$comments_query = "SELECT count(*) as total FROM comments WHERE user_id='$_id'";
$result=mysqli_query($db,$comments_query);
$total=$result->fetch_assoc();
$user['comments_count']= $total['total'];

if($user['recent_comment_1']){
    $id_1 = $user['recent_comment_1'];
    $restaurant_query_1 = "SELECT name, review_star,address, main_area, main_pic,_id FROM restaurants WHERE _id='$id_1' LIMIT 1";
    $query_result_1 = mysqli_query($db, $restaurant_query_1);
    $restaurant_1 = $query_result_1->fetch_assoc();
    $user['recent_visit_1'] = $restaurant_1;
}
if($user['recent_comment_2']){
    $id_2 = $user['recent_comment_2'];
    $restaurant_query_2 = "SELECT name, review_star,address, main_area, main_pic,_id FROM restaurants WHERE _id='$id_2' LIMIT 1";
    $query_result_2 = mysqli_query($db, $restaurant_query_2);
    $restaurant_2 = $query_result_2->fetch_assoc();
    $user['recent_visit_2'] = $restaurant_2;
}if($user['recent_comment_3']){
    $id_3 = $user['recent_comment_3'];
    $restaurant_query_3 = "SELECT name, review_star,address, main_area, main_pic,_id FROM restaurants WHERE _id='$id_3' LIMIT 1";
    $query_result_3 = mysqli_query($db, $restaurant_query_3);
    $restaurant_3 = $query_result_3->fetch_assoc();
    $user['recent_visit_3'] = $restaurant_3;
}
if(!$user){
    $response = [
        "ok"=>false,
        "data"=> new stdClass()
    ];
    send_response($response,203);
}
$response = [
    "ok"=>true,
    "data"=>$user,
];
send_response($response,200);
?>