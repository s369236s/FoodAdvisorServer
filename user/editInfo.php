<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
require_once('../module/jwt/src/JWT.php');

use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
sleep(1); 
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);

$token=null;
if(isset($_POST['jid']))
$token = $_POST['jid'];
if (!$token) {
    $response = [
        "ok" => false,
        "data"=>new stdClass(),
        "error"=>"no accessToken"
    ];
    send_response($response, 203);
}

if (!isset($_POST)) {
    $response = [
        "ok" => false,
    ];
    send_response($response, 203);
}

$payload = [];

try{
    JWT::$leeway = 60;
    $decoded = JWT::decode($token,REFRESH_TOKEN_SECRET,array('HS256'));
    foreach (json_decode(json_encode($decoded), true) as $key => $value) {
        $payload[$key] =  $value;
    }
}catch (Exception $e) {  
    $response = [
        "ok"=>false,
        "error"=>$e->getMessage(),
        "data"=>new stdClass(),
        "accessToken" => $token
    ];
    send_response($response,203);
}

$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$jwt_data = $payload['data'];
$email = mysqli_real_escape_string($db, $jwt_data['email']);
$username = mysqli_real_escape_string($db, $jwt_data['username']);
$query = "SELECT username,address,intro,pic,_id FROM users WHERE email='$email'";
$result = mysqli_query($db, $query);
$user = $result->fetch_assoc();
if(!$user){
    $response = [
        "ok"=>false,
        "data"=>new stdClass(),
        "error"=>"auth error"
    ];
    send_response($response,203);
}
$valid_errors=[];
$info = $_POST['text'];
$user_id = $user['_id'];
if($_POST['type']=="editName"){

    if(empty($info)){
        $response = [
            "ok" => false,
            "error"=>"名稱不能空"
        ];
        send_response($response, 203);
    }

    $update_query = "UPDATE users SET username='$info' WHERE _id='$user_id'";
    $status= "username updated sucessfully";
}

if($_POST['type']=="editPic"){
    if(empty($info)){
        array_push($valid_errors, "圖片不能空");
        $response = [
            "ok" => false,
            "data" => $data,
            "errors" => $valid_errors
        ];
        send_response($response, 203);
    }
    
    $pic = [
        "data"=>decode_file($info),
        "size"=>get_file_size($info),
        "type"=>get_file_type($info)
    ];
    
    if($pic['type']==='wrong_type'){
        $response = [
            "ok" => false,
            "data" => $valid_errors
        ];
        send_response($response, 203);
    }
    
    if($pic['size']>2){
        array_push($valid_errors, "圖片太大");
        $response = [
            "ok" => false,
            "data" => $valid_errors,
            "errors" => $valid_errors
        ];
        send_response($response, 203);
    }
    $pic_path=  mysqli_real_escape_string($db,save_file($pic['data'],$pic['type']));
    $_id = bin2hex(openssl_random_pseudo_bytes(8));
    $comment_date = date("Y-m-d H:i:s");
    $update_query = "UPDATE users SET pic='$pic_path' WHERE _id='$user_id'";
    $status= "pic updated sucessfully";
}

if($_POST['type']=="editAddress"){
    $update_query = "UPDATE users SET address='$info' WHERE _id='$user_id'";
    $status= "address updated sucessfully";
}
if($_POST['type']=="editIntro"){
    $update_query = "UPDATE users SET intro='$info' WHERE _id='$user_id'";
    $status= "intro updated sucessfully";
}
mysqli_query($db, $update_query);
$response = [
    "ok" => true,
        "status"=>$status,
        "e"=>$info,
        "id"=>$user_id
];
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

?>