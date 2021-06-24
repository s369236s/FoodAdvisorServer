<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../controller/send_response.php');
$token= null;
// using localStorage stores token
//!!!! This page is useless
if(isset($_COOKIE['jid']))
{
    $token = $_COOKIE['jid'];
    $cookie_options = array (
        'expires' => time() - 60*60*24*7,
        'path' => '/',
        );
    setcookie("jid",$token,$cookie_options);
    $response = [
        "ok"=>true,
        "data"=>$_COOKIE['jid']
    ];
    send_response($response,200);
}

$response = [
    "ok"=>false,
    "data"=>$_COOKIE['jid']
];
send_response($response,203);