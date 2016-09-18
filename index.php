<?php

define('ROOT', dirname(__FILE__));

$cont = isset($_REQUEST['cont']) ? ucfirst(trim($_REQUEST['cont'])) . 'Controller' : '';
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';

if (empty($cont) || empty($act)) {
    die('Bad Request!!!');
}

$classFile = ROOT."/controllers/{$cont}.php";
if (file_exists($classFile)) {

    require_once $classFile;
    $namespaceClass = '\txapi\controllers\\' . $cont;
    $classObj = new $namespaceClass;

    if (method_exists($classObj, $act)) {
        $classObj->_params = $_REQUEST;
        $classObj->{$act}();
    } else {
        die('Api Not Exists!!!');
    }
} else {
    die('Cont Not Exists!!!');
}
