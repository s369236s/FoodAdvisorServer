<?php
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
// $req_body = json_decode(file_get_contents('php://input'));
sleep(1);
if (!isset($_POST)) {
    $response = [
        "ok" => false,
        "data" => $data
    ];
    send_response($response, 203);
}
$data = [];
$valid_errors=[];

foreach ($_POST as $key => $value) {
    $data[$key] = $value;
}

$user_id = mysqli_real_escape_string($db, $data['user_id']);
$name = mysqli_real_escape_string($db, $data['name']);
$address = mysqli_real_escape_string($db, $data['address']);
$number = mysqli_real_escape_string($db, $data['number']);
$intro = mysqli_real_escape_string($db, $data['intro']);
$main_area = mysqli_real_escape_string($db, $data['main_area']);
if (empty($name)) {
    array_push($valid_errors, "餐廳名稱?");
}
if (empty($address)) {
    array_push($valid_errors, "餐廳地址?");
}
if (empty($number)) {
    array_push($valid_errors, "餐廳電話?");
}
if (empty($intro)) {
    array_push($valid_errors, "餐廳介紹?");
}

if($valid_errors){
    $response = [
        "ok" => false,
        "data" => $data,
        "errors" => $valid_errors,
    ];
    send_response($response,203);
} 


if(!$data['main_pic']||!$data['other_pic_1']||!$data['other_pic_2']){
    array_push($valid_errors, "圖片?");
    $response = [
        "ok" => false,
        "data" => $data,
        "errors" => $valid_errors
    ];
    send_response($response, 203);
}

$main_pic = [
    "data"=>decode_file($data['main_pic']),
    "size"=>get_file_size($data['main_pic']),
    "type"=>get_file_type($data['main_pic'])
];
$other_pic_1 = [
    "data"=>decode_file($data['other_pic_1']),
    "size"=>get_file_size($data['other_pic_1']),
    "type"=>get_file_type($data['other_pic_1'])
];

$other_pic_2 = [
    "data"=>decode_file($data['other_pic_2']),
    "size"=>get_file_size($data['other_pic_2']),
    "type"=>get_file_type($data['other_pic_2'])
];

if($main_pic['type']==='wrong_type'||$other_pic_1['type']==='wrong_type'||$other_pic_2['type']==='wrong_type'){
    $response = [
        "ok" => false,
        "data" => $data
    ];
    send_response($response, 203);
}

if($main_pic['size']>2||$other_pic_1['size']>2||$other_pic_2['size']>2){
    array_push($valid_errors, "圖片太大");
    $response = [
        "ok" => false,
        "data" => $data,
        "errors" => $valid_errors
    ];
    send_response($response, 203);
}



if (count($valid_errors) == 0) {
  $main_pic_path=  mysqli_real_escape_string($db,save_file($main_pic['data'],$main_pic['type']));
  $other_pic_1_path=  mysqli_real_escape_string($db,save_file($other_pic_1['data'],$other_pic_1['type']));
  $other_pic_2_path= mysqli_real_escape_string($db,save_file($other_pic_2['data'],$other_pic_2['type'])); 
  $_id = bin2hex(openssl_random_pseudo_bytes(8));
  $create_query = "INSERT INTO restaurants (id,_id, name, review_star, Introduction, address, phone_number, main_area, hours, main_pic, other_pic_1,other_pic_2, owner_id) 
        VALUES(null,'$_id','$name', 0, '$intro','$address','$number','$main_area','0','$main_pic_path','$other_pic_1_path','$other_pic_2_path','$user_id')";
    mysqli_query($db, $create_query);


    $response = [
        "ok" => true,
        "data" => $data,
        "query"=>$create_query
    ];
}


send_response($response, 200);



function get_file_type($file)
{
    $file_type = explode(',', $file, 2)[1];
    $file_type = base64_decode($file_type);
    $file_type = getimagesizefromstring($file_type);
    switch($file_type['mime']){
        case "image/png":
            $file_type="png";
            break;
        case "image/jpeg":
            $file_type="jpeg";
            break;
        case "image/jpg":
            $file_type="jpg";
            break;
        default:
            $file_type="wrong_type";
            break;
    }
    return $file_type;
}

function get_file_size($data)
{
    return (mb_strlen($data) * 3 / 4) / (1024 * 1024);
}

function decode_file($data)
{
    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    return $data;
}

function save_file($image,$file_type){
    $rd =  bin2hex(openssl_random_pseudo_bytes(10));
    $filename = date('Y-m-d-H-i-s');
    file_put_contents(STATIC_FOLDER.'/'.$filename.$rd.'.'.$file_type,base64_decode($image));
    return 'static/'.$filename.$rd.'.'.$file_type;
}