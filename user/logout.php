<?php 
header('Access-Control-Allow-Credentials: true');
$token= null;

if(isset($_COOKIE['jid']))
{
    $token = $_COOKIE['jid'];
    $arr_cookie_options = array (
        'expires' => time() - 60*60*24*7,
        'path' => '/',
        'secure' => true,     // or false
        );
    setcookie("jid",$token,$arr_cookie_options);
    $res = [
        "ok"=>true,
        "data"=>$_COOKIE['jid']
    ];
    sendResponse($res,200);
}
else{
    $res = [
        "ok"=>false,
        "data"=>$_COOKIE['jid']
    ];
    sendResponse($res,203);
    die();
}

function sendResponse($json, $code)
{
    // sleep(1);
    echo json_encode($json);
    http_response_code($code);
    die();
}
?>