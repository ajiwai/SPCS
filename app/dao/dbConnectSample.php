<?php

define("SERVER", "localhost");
define("DB_NAME", "test_db");
define("USERNAME", "root");
define("PASSWORD", "mysql");

try{
  //文字エンコーディングを必ず指定する
// $options = array(
//     PDO::MYSQL_ATTR_READ_DEFAULT_FILE  => '/etc/my.cnf',
// ); 

  $dbh = new PDO("mysql:host=" . SERVER . ";dbname=" . DB_NAME . ";charset=utf8", USERNAME, PASSWORD);
  //$dbh = new PDO("mysql:host=" . SERVER . ";dbname=" . DB_NAME , USERNAME, PASSWORD, $options);
 
  // 静的プレースホルダを指定
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 
  $stmt = $dbh->prepare("select * from M_SITE WHERE ID = ? AND SITE_NM = ?;");
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  
  $a = 1;
  $b = "rikunabi";
 
  $stmt->bindParam(1, $a, PDO::PARAM_INT);
  $stmt->bindParam(2, $b, PDO::PARAM_STR);
 
  $stmt->execute();
 
  while ($row = $stmt->fetch()) {
    echo $row["ID"];
    echo $row["SITE_NM"];
    echo $row["URL"];
  }
 
  $dbh = null;
 
} catch(PDOException $e){
  echo $e->getMessage();
}