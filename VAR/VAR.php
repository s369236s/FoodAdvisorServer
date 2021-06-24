<?php

const MODE = "dev";

const ACCESS_TOKEN_SECRET= "imaccesstoken";

const REFRESH_TOKEN_SECRET= "imrefreshtoken";
const STATIC_FOLDER = "../../../htdocs/FoodAdvisorServer/static";
const DB = MODE === "dev" ? "localhost" : "foodadvisor.ddns.net:3306";

const DBTABLE = "dev" ? "test" : "foodadvisor"; 
const DBUSERNAME = "dev" ? "test" : "foodadvisor"; 
const DBPASSWORD = "dev" ? "yzu" : "yzu"; 

const googleAPIKey = "AIzaSyCy7RNwB5rTG7rkpDq41cpvFwNLh3_Lohk";