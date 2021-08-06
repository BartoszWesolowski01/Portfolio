<?php
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_NAME','');
define('DB_PASS','');

try{
    $dbh = new PDO("mysql:host=".DB_HOST."; dbname=".DB_NAME, DB_USER, DB_PASS);
    $dbh->exec("set names utf8");
}

catch(PDOException $e){
    exit("Error: ".$e->getMessage());
}
?>