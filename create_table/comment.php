<?php
require_once('../VAR/VAR.php');
$db = new mysqli(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$create_query = "CREATE TABLE comments(
 id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  _id  VARCHAR(16) NOT NULL,
  user_id  VARCHAR(16) NOT NULL,
  restaurant_id  VARCHAR(16) NOT NULL,
  review_star  FLOAT NOT NULL,
  price_star FLOAT NOT NULL,
  food_star  FLOAT NOT NULL,
  speed_star  FLOAT NOT NULL,
  pic  VARCHAR(100) NOT NULL,
  title  VARCHAR(30) NOT NULL,
  content  VARCHAR(150) NOT NULL,
  comment_date DATE NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$db->query($create_query);
echo $db->error;
$db->close();
