<?php

require_once 'init.php';

$pageName = 'CountList';

//require APP_ROOT_DIR . '/model' . DS . $pageName . MODEL_BASE_NAME;
require APP_ROOT_DIR . DS . 'controller' . DS . $pageName . CONTROLLER_BASE_NAME;
$className = $pageName . 'Controller';
$class = new $className();
$postData = '';

$_POST['search_sel_cate'] = '3';

if(isset($_POST)){
	$postData = $_POST;
}
$class->view($postData);

//$requestUri = '';

// switch ($requestUri) {
// case '':
// case 'index.php':
// 	// require APP_ROOT_DIR . '/controller' . DS . 'CountListController.php';
// 	// $class = new CountListController();
// 	require APP_ROOT_DIR . '/model' . DS . 'CountListModel.php';
// 	$class = new CountListModel();
// 	$postData = '';
// 	if(isset($_POST)){
// 		$postData = $_POST;
// 	}
// 	$class->view($postData);
// 	break;
// default:
// 	break;
// }

