<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
require_once('../controller/create_jwt.php');
require_once('../module/jwt/src/JWT.php');
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;

$token=null;
if(isset($_COOKIE['jid']))
$token = $_COOKIE['jid'];

if (!$token) send_response('no cookie', 203);


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
$data = $payload['data'];
$email = mysqli_real_escape_string($db, $data['email']);
$username = mysqli_real_escape_string($db, $data['username']);
$query = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($db, $query);
$user = $result->fetch_assoc();
if(!$user){
    $response = [
        "ok"=>false,
        "data"=>new stdClass(),
        "accessToken" => ""
    ];
    send_response($response,203);
}
$response = [
    "ok"=>true,
    "_id"=>$user['_id'],
    "accessToken" => create_access_token($username,$email)
];
send_response($response,200);
