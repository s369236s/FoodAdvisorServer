<?php

const MODE = "dev";

const ACCESS_TOKEN_SECRET= "imaccesstoken";

const REFRESH_TOKEN_SECRET= "imrefreshtoken";

const DB = MODE === "dev" ? "localhost" : "foodadvisor.ddns.net:3306";

const DBTABLE = "dev" ? "test" : "foodadvisor"; 
const DBUSERNAME = "dev" ? "test" : "foodadvisor"; 
const DBPASSWORD = "dev" ? "yzu" : "yzu"; 