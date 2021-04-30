<?php
$db = mysqli_connect('localhost', 'test', 'yzu', 'test');
$req = json_decode(file_get_contents('php://input'));
if (isset($req)) {
    $validErrors = [];
    $data =  [];
    $req->password =  password_hash($req->password,PASSWORD_DEFAULT);
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
    
    if($validErrors){
       $res=[
        "ok"=>false,
        "data"=>new stdClass(),
        "valid"=>$validErrors
    ];
    echo json_encode($res);
    die();
    }
    $res=[
        "ok"=>true,
        "data"=>$data
    ];
    echo json_encode($res);
} else {

}
