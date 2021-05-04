<?php 
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$req_body = json_decode(file_get_contents('php://input'));

$data = [];

$data['name']=$_POST['name'];
$data['address']=$_POST['address'];
$data['number']=$_POST['number'];
$data['intro']=$_POST['intro'];
$data['main_pic']=$_POST['main_pic'];
$data['other_pic_1']=$_POST['other_pic_1'];
$data['other_pic_2']=$_POST['other_pic_2'];   

$response = [
    "ok"=>true,
    "data"=>$data
];

send_response($response,200);

?>