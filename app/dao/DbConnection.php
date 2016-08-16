<?php

class DbConnection{

    public function __construct() {
        define('SERVER', 'localhost');
        define('DB_NAME', 'spcs_db');
        define('USERNAME', 'root');
        define('PASSWORD', '!root');
    }

    public function getConnection() {
        try{

            //$dbh = new PDO('mysql:host=' . SERVER . ';dbname=' . DB_NAME . ';charset=utf8', USERNAME, PASSWORD);
            //日本語文字化け対策
            $options = array(
                PDO::MYSQL_ATTR_READ_DEFAULT_FILE => '/etc/my.cnf',
            );
            $dbh = new PDO('mysql:host=' . SERVER . ';dbname=' . DB_NAME . ';charset=utf8', USERNAME, PASSWORD, $options);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 静的プレースホルダを指定
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $dbh;
   
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }
}
