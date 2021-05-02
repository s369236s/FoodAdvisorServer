<?php
header('Access-Control-Allow-Credentials: true');
const ACCESS_TOKEN_SECRET = "imaccesstoken";
const REFRESH_TOKEN_SECRET = "imrefreshtoken";
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;

require_once('../vendor/autoload.php');



$headers = apache_request_headers();

$token=null;
// if(isset($headers['Authorization']))
// $token = str_replace('Bearer ','',$headers['Authorization']); 
if(isset($_COOKIE['jid']))
$token = $_COOKIE['jid'];

if (!$token) sendResponse(new stdClass(), 203);

$payload = [];
$data=[];
try {
    JWT::$leeway = 60;
    $decoded = JWT::decode($token, REFRESH_TOKEN_SECRET, array('HS256'));
    foreach (json_decode(json_encode($decoded), true) as $key => $value) {
        $payload[$key] =  $value;
    }
} catch (SignatureInvalidException $e) {  //签名不正确
    $res = [
        "ok"=>false,
        "err"=>$e->getMessage(),
        "data"=>new stdClass(),
        "accessToken" => json_encode($payload)
    ];
    sendResponse($res,203);
} catch (BeforeValidException $e) {  // 签名在某个时间点之后才能用
    $res = [
        "ok"=>false,
        "err"=>$e->getMessage(),
        "data"=>new stdClass(),
        "accessToken" => "BeforeValidException"
    ];
    sendResponse($res,203);
} catch (ExpiredException $e) {  // token过期
    $res = [
        "ok"=>false,
        "err"=>$e->getMessage(),
        "data"=>new stdClass(),
        "accessToken" => "ExpiredException"
    ];
    sendResponse($res,203);
} catch (Exception $e) {  //其他错误
    $res = [
        "ok"=>false,
        "err"=>$e->getMessage(),
        "data"=>new stdClass(),
        "accessToken" => $token
    ];
    sendResponse($res,203);
}
// $db = mysqli_connect('localhost', 'test', 'yzu', 'test');
$db = mysqli_connect('db4free.net:3306', 'foodadvisor123', 'foodadvisor', 'foodadvisor');

$data = $payload['data'];
$email = mysqli_real_escape_string($db, $data['email']);
$username = mysqli_real_escape_string($db, $data['username']);
$db = mysqli_connect('localhost', 'test', 'yzu', 'test');
$query = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($db, $query);
$user = $result->fetch_assoc();

if(!$user){
    $res = [
        "ok"=>false,
        "data"=>new stdClass(),
        "accessToken" => ""
    ];
    sendResponse($res,203);
}

$res = [
    "ok"=>true,
    "data"=>new stdClass(),
    "accessToken" => createAccessToken($username,$email)
];
sendResponse($res,200);
$arr_cookie_options = array (
    'expires' => time() + 60*60*24*7,
    'path' => '/',
    'secure' => true,     // or false
    'httponly' => true,    // or false
    );
// setcookie("jid",createRefreshToken($username,$email),$arr_cookie_options);



function sendResponse($json, $code)
{
    // sleep(1);
    echo json_encode($json);
    http_response_code($code);
    die();
}
function createAccessToken($username, $email)
{
    return JWT::encode([
        "iat" => time(),
        "nbf" => time(),
        "exp" => time() + 7200, "data" => [
            "email" => $email, "username" => $username
        ]
    ], ACCESS_TOKEN_SECRET);
}

function createRefreshToken($username, $email)
{
    return JWT::encode([
        "iat" => time(),
        "nbf" => time(),
        "exp" => time() + 60*60*24*7, "data" => [
            "email" => $email, "username" => $username
        ]
    ], REFRESH_TOKEN_SECRET);
}