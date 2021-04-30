<?php 
$db = mysqli_connect('localhost', 'test', 'yzu', 'test');
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
        $user= $result->fetch_assoc();
        if(!$user){
            array_push($validErrors, "信箱不存在");
            $res = [
                "ok" => false,
                "data" => new stdClass(),
                "valid" => $validErrors
            ];
            sendResponse($res, 203);
        }
        $dbPassword = $user['password'];

        if ( password_verify($password, $dbPassword)) {
            $res = [
                "ok" => true,
                "data" => new stdClass(),
                "valid" => $validErrors
            ];
        sendResponse($res, 200);
        }else {
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
