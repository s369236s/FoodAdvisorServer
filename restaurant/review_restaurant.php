<?php   
header('Access-Control-Allow-Credentials: true');
require_once('../VAR/VAR.php');
require_once('../controller/send_response.php');
$db = mysqli_connect(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
sleep(1);
if (!isset($_POST)) {
    $response = [
        "ok" => false,
        "data" => $data
    ];
    send_response($response, 203);
}
$data = [];
$valid_errors=[];

foreach ($_POST as $key => $value) {
    $data[$key] = $value;
}

$review_star = mysqli_real_escape_string($db, floatval($data['review']));
$title = mysqli_real_escape_string($db, $data['title']);
$content = mysqli_real_escape_string($db, $data['content']);
$food_star = mysqli_real_escape_string($db, floatval($data['food']));
$speed_star = mysqli_real_escape_string($db, floatval($data['speed']));
$price_star = mysqli_real_escape_string($db, floatval($data['price']));
$restaurant_id = mysqli_real_escape_string($db, $data['restaurant_id']);
$user_id = mysqli_real_escape_string($db, $data['user_id']);
if (empty($review_star)) {
    array_push($valid_errors, "評分?");
}
if (empty($title)) {
    array_push($valid_errors, "標題?");
}
if (empty($content)) {
    array_push($valid_errors, "內容?");
}
if (empty($food_star)) {
    array_push($valid_errors, "食物?");
}
if (empty($speed_star)) {
    array_push($valid_errors, "速度?");
}
if (empty($price_star)) {
    array_push($valid_errors, "價格?");
}

if($valid_errors){
    $response = [
        "ok" => false,
        "data" => $data,
        "errors" => $valid_errors,
    ];
    send_response($response,203);
} 

if(!$data['pic']){
    array_push($valid_errors, "圖片?");
    $response = [
        "ok" => false,
        "data" => $data,
        "errors" => $valid_errors
    ];
    send_response($response, 203);
}


$pic = [
    "data"=>decode_file($data['pic']),
    "size"=>get_file_size($data['pic']),
    "type"=>get_file_type($data['pic'])
];

if($pic['type']==='wrong_type'){
    $response = [
        "ok" => false,
        "data" => $data
    ];
    send_response($response, 203);
}

if($pic['size']>2){
    array_push($valid_errors, "圖片太大");
    $response = [
        "ok" => false,
        "data" => $data,
        "errors" => $valid_errors
    ];
    send_response($response, 203);
}

if (count($valid_errors) == 0) {
    $pic_path=  mysqli_real_escape_string($db,save_file($pic['data'],$pic['type']));
    $_id = bin2hex(openssl_random_pseudo_bytes(8));
    $create_query = "INSERT INTO comments (id, _id, content, title, review_star, food_star, speed_star, pic, user_id, restaurant_id) VALUES (NULL, '$_id', '$content', '$title', '$review_star', '$food_star', '$speed_star', '$pic_path', '$user_id', '$restaurant_id')";
      mysqli_query($db, $create_query);

      $response = [
          "ok" => true,
          "data" => $data,
          "query"=>$create_query
      ];
  }
  

  send_response($response, 200);




function get_file_type($file)
{
    $file_type = explode(',', $file, 2)[1];
    $file_type = base64_decode($file_type);
    $file_type = getimagesizefromstring($file_type);
    switch($file_type['mime']){
        case "image/png":
            $file_type="png";
            break;
        case "image/jpeg":
            $file_type="jpeg";
            break;
        case "image/jpg":
            $file_type="jpg";
            break;
        default:
            $file_type="wrong_type";
            break;
    }
    return $file_type;
}

function get_file_size($data)
{
    return (mb_strlen($data) * 3 / 4) / (1024 * 1024);
}

function decode_file($data)
{
    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    return $data;
}

function save_file($image,$file_type){
    $rd =  bin2hex(openssl_random_pseudo_bytes(10));
    $filename = date('Y-m-d-H-i-s');
    file_put_contents(STATIC_FOLDER.'/'.$filename.$rd.'.'.$file_type,base64_decode($image));
    return 'static/'.$filename.$rd.'.'.$file_type;
}
?>