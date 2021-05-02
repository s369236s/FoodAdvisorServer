<?php

header('Access-Control-Allow-Credentials: true');
const ACCESS_TOKEN_SECRET = "imaccesstoken";
const REFRESH_TOKEN_SECRET　 = "imrefreshtoken";

use Firebase\JWT\JWT;

require_once('../vendor/autoload.php');

// $db = mysqli_connect('localhost', 'test', 'yzu', 'test');
$db = mysqli_connect('db4free.net:3306', 'foodadvisor123', 'foodadvisor', 'foodadvisor');

$req = json_decode(file_get_contents('php://input'));
if (isset($req)) {
    $validErrors = [];
    $data =  [];
    foreach ($req as $key => $value) {
        $data[$key] = mysqli_real_escape_string($db, $value);
    }
    $email = mysqli_real_escape_string($db, $data['email']);
    $password = mysqli_real_escape_string($db, $data['password']);
    if (empty($email)) {
        array_push($validErrors, "信箱?");
    }
    if (empty($password)) {
        array_push($validErrors, "密碼?");
    }
    if ($validErrors) {
        $res = [
            "ok" => false,
            "data" => new stdClass(),
            "valid" => $validErrors
        ];
        sendResponse($res, 203);
    }
    if (count($validErrors) == 0) {
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($db, $query);
        $user = $result->fetch_assoc();
        if (!$user) {
            array_push($validErrors, "信箱不存在");
            $res = [
                "ok" => false,
                "data" => new stdClass(),
                "valid" => $validErrors
            ];
            sendResponse($res, 203);
        }
        $dbPassword = $user['password'];
        $dbUsername = $user['username'];
        if (password_verify($password, $dbPassword)) {
            $accessToken = createAccessToken($dbUsername,$email);
            $refreshToken = createRefreshToken($dbUsername,$email);
          
            $arr_cookie_options = array (
                'expires' => time() + 60*60*24*7,
                'path' => '/',
                'secure' => true,     // or false
                );
            setcookie("jid",$refreshToken,$arr_cookie_options);
            $res = [
                "ok" => true,
                "data" => new stdClass(),
                "valid" => $validErrors,
                "accessToken" => $accessToken,
            ];
            sendResponse($res, 200);
        } else {
            array_push($validErrors, "密碼錯誤");
            $res = [
                "ok" => false,
                "data" => new stdClass(),
                "valid" => $validErrors,
            ];
            sendResponse($res, 203);
        }
    }
}

function sendResponse($json, $code)
{
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
    ], REFRESH_TOKEN_SECRET　);
}
