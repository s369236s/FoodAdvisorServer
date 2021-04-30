<?php
$data = json_decode(file_get_contents('php://input'));
if (isset($data)) {
    $res=  [];
    foreach($data as $key=>$value){
        $res[$key] = $value;
    }
    echo json_encode($res);
    echo $data['password'];
} else {
}
?>