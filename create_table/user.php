<?php 
require_once('../VAR/VAR.php');
$db = new mysqli(DB, DBUSERNAME, DBPASSWORD, DBTABLE);
$create_query = "CREATE TABLE users (
    id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    _id varchar(16) NOT NULL,
    username varchar(16) NOT NULL,
    email varchar(100) NOT NULL,
    password varchar(100) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$db->query($create_query);
$db->close();