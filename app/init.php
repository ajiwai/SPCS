<?php

if (!defined('DS')) {
//    define('DS', DIRECTORY_SEPARATOR);
    define('DS', '/');
}
if (!defined('APP_ROOT_DIR')) {
//    define('DS', DIRECTORY_SEPARATOR);
    define('APP_ROOT_DIR', '/var/www/html/SPCS/app');
}
if (!defined('CONTROLLER_BASE_NAME')) {
    define('CONTROLLER_BASE_NAME', 'Controller.php');
}
if (!defined('MODEL_BASE_NAME')) {
    define('MODEL_BASE_NAME', 'Model.php');
}
if (!defined('VIEW_BASE_NAME')) {
    define('VIEW_BASE_NAME', '.tmpl');
}
if (!defined('VIEW_DIR')) {
    define('VIEW_DIR', 'View');
}

