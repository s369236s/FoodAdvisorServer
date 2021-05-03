<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
require_once('../controller/create_jwt.php');
require_once('../module/jwt/src/JWT.php');

$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$req_body = json_decode(file_get_contents('php://input'));
if(isset($req_body)){
    $valid_errors = [];
    $data =  [];
    foreach ($req_body as $key => $value) {
        $data[$key] = mysqli_real_escape_string($db, $value);
    }
    $email = mysqli_real_escape_string($db, $data['email']);
    $password = mysqli_real_escape_string($db, $data['password']);
    if(empty($email))
    array_push($valid_errors,"信箱?");
    if(empty($password))
    array_push($valid_errors,"密碼?");
    if($valid_errors){
        $response = [
            "ok" => false,
            "data" => new stdClass(),
            "errors" => $valid_errors
        ];
        send_response($response,203);
    }

    if(count($valid_errors)===0){
        $db_query = "SELECT * FROM users WHERE email='$email'";
        $query_result = mysqli_query($db,$db_query);
        $user = $query_result->fetch_assoc();

        if(!$user){
            array_push($valid_errors,"信箱不存在");
            $response=[
                "ok"=>false,
                "data"=> new stdClass(),
                "errors"=>$valid_errors
            ];
            send_response($response,203);
        }

        $user_username = $user['username'];
        $user_password = $user['password'];
        
        if(!password_verify($password,$user_password)){
            array_push($valid_errors, "密碼錯誤");
            $response = [
                "ok" => false,
                "data" => new stdClass(),
                "errors" => $valid_errors,
            ];
            send_response($response, 203);
        }
        $access_token = create_access_token($user_username,$email);
        $refresh_token = create_refresh_token($user_username,$email);
        $cookie_options = array (
            'expires' => time() + 60*60*24*7,
            'path' => '/',
            );
        setcookie("jid",$refresh_token,$cookie_options);
        $response = [
            "ok"=>true,
            "data"=> new stdClass(),
            "errors"=>$valid_errors,
            "accessToken" =>$access_token
        ];
        send_response($response,200);
    }
   
}

?>