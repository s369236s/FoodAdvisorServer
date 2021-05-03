<?php 
function send_response($json,$code){
    echo json_encode($json);
    http_response_code($code);
    die();
}
?>