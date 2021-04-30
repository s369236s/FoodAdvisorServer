<?php
$db = mysqli_connect('localhost', 'test', 'yzu', 'test');
$req = json_decode(file_get_contents('php://input'));

if (isset($req)) {
    $validErrors = [];
    $data =  [];

    foreach ($req as $key => $value) {
        $data[$key] = mysqli_real_escape_string($db, $value);
    }
    if (empty($data['username'])) {
        array_push($validErrors, "Username can't be empty");
    }
    if (empty($data['password'])) {
        array_push($validErrors, "Password can't be empty");
    }
    if (empty($data['email'])) {
        array_push($validErrors, "Email can't be empty");
    }
    if (empty($data['confirmPassword'])) {
        array_push($validErrors, "ConfirmPassword can't be empty");
    }
    if ($data['password'] != $data['confirmPassword'] && !empty($data['password']) && !empty($data['confirmPassword'])) {
        array_push($validErrors, "ConfirmPassword do not match with password");
    }

    if ($validErrors) {
        $res = [
            "ok" => false,
            "data" => new stdClass(),
            "valid" => $validErrors
        ];
        sendResponse($res, 203);
    }

    $user_check_query = "SELECT * FROM users WHERE username=".$data['username']." OR email=".$data['email']." LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // if user exists
        if ($user['username'] == $data['username']) {
            array_push($validErrors, "Username already exists");
        }

        if ($user['email'] == $data['email']) {
            array_push($validErrors, "email already exists");
        }
        $res = [
            "ok" => false,
            "data" => new stdClass(),
            "valid" => $validErrors
        ];
        sendResponse($res, 203);
    }

    if (count($validErrors) == 0) {
        $hashPassword =  password_hash($data['password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password) 
        VALUES(".$data['username'].", ".$data['email'].", '$hashPassword')";
        mysqli_query($db, $query);
        $res = [
            "ok" => true,
            "data" => $data
        ];
        sendResponse($res, 201);
    }
}

function sendResponse($json, $code)
{
    echo json_encode($json);
    http_response_code($code);
    die();
}
