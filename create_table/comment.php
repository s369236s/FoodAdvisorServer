<?php 
$sql = "CREATE TABLE comments  (  id  INT NOT NULL AUTO_INCREMENT 
,  _id  VARCHAR(16) NOT NULL ,  user_id  VARCHAR(16) NOT NULL 
,  restaurant_id  VARCHAR(16) NOT NULL ,  review_star  FLOAT NOT NULL 
,  food_star  FLOAT NOT NULL ,  speed_star  FLOAT NOT NULL ,
  pic  VARCHAR(100) NOT NULL ,  title  VARCHAR(30) NOT NULL ,
  content  VARCHAR(150) NOT NULL ,
   PRIMARY KEY ( id )) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci";
