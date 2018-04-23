<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 15:32
 */
ini_set('date.timezone', 'PRC');
//web日志保存的位置
define('START_TIME', microtime(true));
define('START_TIME_DATE', date('Y-m-d H:i:s', START_TIME));
define("VPATH", dirname(__DIR__));

//代理模式下ip纠正
if (isset($_SERVER['HTTP_X_REAL_YES_IP']) && $_SERVER['HTTP_X_REAL_YES_IP']) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_YES_IP'];
}

//矫正apache在某些环境下返回全路径的错误
if (strpos($_SERVER['REQUEST_URI'], '://') !== false) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

define(
    '__URL__', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST']
        : $_SERVER['SERVER_ADDR']) . $_SERVER['REQUEST_URI']
);

//定义是否AJAX请求
define(
    'IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false
);

define('EXT', '.class.php');//类文件后缀
define('CLHAPP_PATH', dirname(__FILE__));
define('CONF_PATH', VPATH . '/Conf/');
define('CORE_PATH', CLHAPP_PATH . '/Core/');
define('COMMON_PATH', CLHAPP_PATH . '/Common/');
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);

include COMMON_PATH . 'functions.php';
//初始化配置
C(include CONF_PATH . 'conf.php');

//测试环境
$testFile = VPATH . '/test';
if (is_file($testFile)) {
    C(include CONF_PATH . 'test.php');
}

include CORE_PATH . 'Clhapp.class.php';
include CORE_PATH . 'Dispatcher.class.php';
include CORE_PATH . 'Action.class.php';
include CORE_PATH . 'Cache.class.php';
include CORE_PATH . 'Db.class.php';
include CORE_PATH . 'Exception.class.php';
include CORE_PATH . 'View.class.php';
include CORE_PATH . 'Log.class.php';
include CORE_PATH . 'Model.class.php';