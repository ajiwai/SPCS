<?php

require_once 'init.php';

$requestUri = str_replace(basename(dirname(__FILE__)) ,'' ,(isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '' );
$requestUri = str_replace('word_cnt_get' ,'' ,$requestUri);
$requestUri = str_replace(basename(dirname(__FILE__)) ,'' ,$requestUri);
$requestUri = str_replace(DS ,'' ,$requestUri);
$pageName = (isset($_GET['p'])) ? $_GET['p'] : 'CountList';

// $referer = $_SERVER['HTTP_REFERER'];
//echo 'requestUri='.$requestUri . '<br>\n';

if(!isset($_SERVER['REQUEST_URI'])){
    if(!isset($argv)){
        require APP_ROOT_DIR . DS . 'task' . DS . 'CrawlerTask.php';
        //echo 'argument not exist!\n';
    }else if($argv[1] == 1){
        require APP_ROOT_DIR . DS . 'task' . DS . 'CrawlerTask.php';
    }else if($argv[1] == 2){
            $date = date('Ymd');
            if(isset($argv[2])){
                $fromYyyymm = $argv[2];
            }else{
                $fromYyyymm = strftime('%Y%m', strtotime($date . '-1 month'));
            }
            if(isset($argv[3])){
                $toYyyymm = $argv[3];
            }else{
                $toYyyymm = strftime('%Y%m', strtotime($date . '-1 month'));
            }
            require APP_ROOT_DIR . DS . 'task' . DS . 'AggregateTask.php';
    }else if($argv[1] == 3){
            $date = date('Ymd');
            if(isset($argv[2])){
                $fromYyyymm = $argv[2];
            }else{
                $fromYyyymm = strftime('%Y%m', strtotime($date . '-1 month'));
            }
            if(isset($argv[3])){
                $toYyyymm = $argv[3];
            }else{
                $toYyyymm = strftime('%Y%m', strtotime($date . '-1 month'));
            }
            require APP_ROOT_DIR . DS . 'dao' . DS . 'DbConnection.php';
            require APP_ROOT_DIR . DS . 'task' . DS . 'AggregateMemorySavingTask.php';
            $conn = new DbConnection();
            $dbh = $conn->getConnection();
            $className = 'AggregateMemorySavingTask';
            $class = new $className($dbh);
            $class->insertData($dbh, $fromYyyymm, $toYyyymm);
            //require APP_ROOT_DIR . DS . 'task' . DS . 'AggregateMemorySavingTask.php';
    }else{
        echo 'argument is bad!\n';
    }
}else{
    if($pageName == 'Api'){

    }else{
        require APP_ROOT_DIR . DS . 'controller' . DS . $pageName . CONTROLLER_BASE_NAME;
        $className = $pageName . 'Controller';
        $class = new $className();
        $postData = '';
        $functionId = (isset($_GET['f'])) ? $_GET['f'] : 1;
        if(isset($_POST)){
//echo var_dump($_POST) . '<br>\n';
            $postData = $_POST;
        }
        $class->view($postData, $functionId);
    }

}

