<?php 
require_once('../VAR/VAR.php');
require_once('../module/jwt/src/JWT.php');
use Firebase\JWT\JWT;
function create_access_token($username, $email)
{
    return JWT::encode([
        "iat" => time(),
        "nbf" => time(),
        "exp" => time() + 7200, "data" => [
            "email" => $email, "username" => $username
        ]
    ], ACCESS_TOKEN_SECRET);
}

function create_refresh_token($username, $email)
{
    return JWT::encode([
        "iat" => time(),
        "nbf" => time(),
        "exp" => time() + 60*60*24*7, "data" => [
            "email" => $email, "username" => $username
        ]
    ], REFRESH_TOKEN_SECRET);
}
?>