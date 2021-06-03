<?php 
require_once('../VAR/VAR.php');
$db = new mysqli(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$create_query = "CREATE TABLE restaurants (
    id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    _id varchar(16) NOT NULL,
    name varchar(100) NOT NULL,
    review_star float NOT NULL,
    food_star float NOT NULL,
    speed_star float NOT NULL,
    price_star float NOT NULL,
    Introduction text NOT NULL,
    address varchar(100) NOT NULL,
    phone_number varchar(100) NOT NULL,
    main_area varchar(100) NOT NULL,
    hours varchar(100) NOT NULL,
    main_pic text NOT NULL,
    other_pic_1 text NOT NULL,
    other_pic_2 text NOT NULL,
    owner_id varchar(16) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$db->query($create_query);
$db->close();