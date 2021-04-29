<?php 

header("Access-Control-Allow-Origin: http://localhost:5000");
if(isset($_POST)){
    $data = json_decode(file_get_contents('php://input'), true);
    print_r($data);
}else{
    echo "wrong";
}

?>