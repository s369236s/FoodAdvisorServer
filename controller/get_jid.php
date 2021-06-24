<?php 
function send_response(){
    $_POST = json_decode(file_get_contents("php://input"),true);
    return $_POST['refreshToken'];
}
?>