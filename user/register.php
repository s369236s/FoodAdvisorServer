<?php
$db = mysqli_connect('localhost', 'test', 'yzu', 'test');
$req = json_decode(file_get_contents('php://input'));

if (isset($req)) {
    $validErrors = [];
    $data =  [];
    foreach ($req as $key => $value) {
        $data[$key] = mysqli_real_escape_string($db, $value);
    }
    $username = mysqli_real_escape_string($db, $data['username']);
    $email = mysqli_real_escape_string($db, $data['email']);
    $password = mysqli_real_escape_string($db, $data['password']);
    $confirm_Password = mysqli_real_escape_string($db, $data['confirmPassword']);
    if (empty($username)) {
        array_push($validErrors, "暱稱?");
    }
    if (empty( $email)) {
        array_push($validErrors, "信箱?");
    }
    if (empty($password)) {
        array_push($validErrors, "密碼?");
    }
    if (empty($confirm_Password)) {
        array_push($validErrors, "重複密碼?");
    }
    if ($password != $confirm_Password && !empty($password) && !empty($confirm_Password)) {
        array_push($validErrors, "密碼錯誤");
    }
    if ($validErrors) {
        $res = [
            "ok" => false,
            "data" => new stdClass(),
            "valid" => $validErrors
        ];
        sendResponse($res, 203);
    }
    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
    if ($user) { 

        if ($user['email'] == $data['email']) {
            array_push($validErrors, "信箱重複");
        }
        $res = [
            "ok" => false,
            "data" => new stdClass(),
            "valid" => $validErrors
        ];
        sendResponse($res, 203);
    }


    if (count($validErrors) == 0) {
        $hashPassword =  password_hash($data['password'], PASSWORD_BCRYPT);
        $query = "INSERT INTO users (username, email, password) 
        VALUES('$username', '$email', '$hashPassword')";
        mysqli_query($db, $query);
        $res = [
            "ok" => true,
            "data" => new stdClass(),
            "valid" => [],
        ];
        sendResponse($res, 201);
    }
}

function sendResponse($json, $code)
{
    sleep(2);
    echo json_encode($json);
    http_response_code($code);
    die();
}
