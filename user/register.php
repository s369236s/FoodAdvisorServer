<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
require_once('../controller/create_jwt.php');
require_once('../module/jwt/src/JWT.php');

$db = mysqli_connect(DB, 'test', 'yzu', 'test');
$req_body = json_decode(file_get_contents('php://input'));
if(isset($req_body)){
    $valid_errors = [];
    $data =  [];
    foreach ($req_body as $key => $value) {
        $data[$key] = mysqli_real_escape_string($db, $value);
    }
    $username = mysqli_real_escape_string($db, $data['username']);
    $email = mysqli_real_escape_string($db, $data['email']);
    $password = mysqli_real_escape_string($db, $data['password']);
    $confirm_Password = mysqli_real_escape_string($db, $data['confirmPassword']);
    if (empty($username)) {
        array_push($valid_errors, "暱稱?");
    }
    if (empty($email)) {
        array_push($valid_errors, "信箱?");
    }
    if (empty($password)) {
        array_push($valid_errors, "密碼?");
    }
    if (empty($confirm_Password)) {
        array_push($valid_errors, "重複密碼?");
    }
    if ($password != $confirm_Password && !empty($password) && !empty($confirm_Password)) {
        array_push($valid_errors, "密碼錯誤");
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        array_push($valid_errors, "格式錯誤");
    }
    if($valid_errors){
        $response = [
            "ok" => false,
            "data" => new stdClass(),
            "errors" => $valid_errors
        ];
        send_response($response,203);
    } 

    $user_check_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $query_result = mysqli_query($db, $user_check_query);
    $user = $query_result->fetch_assoc();

    if ($user) { 
        if ($user['email'] === $email) {
            array_push($valid_errors, "信箱重複");
        }
        $response = [
            "ok" => false,
            "data" => new stdClass(),
            "errors" => $valid_errors
        ];
        send_response($response, 203);
    }

    if (count($valid_errors) == 0) {
        $hashPassword =  password_hash($data['password'], PASSWORD_BCRYPT);
        $_id = bin2hex(openssl_random_pseudo_bytes(8));
        $create_query = "INSERT INTO users (_id,username, email, password) 
        VALUES('$_id','$username', '$email', '$hashPassword')";
        mysqli_query($db, $create_query);
        $response = [
            "ok" => true,
            "data" => new stdClass(),
            "errors" => [],
        ];
        send_response($response, 201);
    }
}
