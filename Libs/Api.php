<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:10
 */
ini_set('date.timezone', 'PRC');
//web日志保存地址
define('START_TIME', microtime(true));
define('START_TIME_DATE', date('Y-m-d H:i:s', START_TIME));
define('VPATH', dirname(__DIR__));

//代理模式下ip纠正
if (isset($_SERVER['HTTP_X_REAL_YES_IP']) && $_SERVER['HTTP_X_REAL_YES_IP']) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_YES_IP'];
}

define(
    '__URL__', ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST']
        : $_SERVER['SERVER_ADDR']) . $_SERVER['REQUEST_URI']
);
define('IS_CLI', false);
define('EXT', '.class.php');//类文件后缀
define('CLHAPP_PATH', dirname(__FILE__));
define('CONF_PATH', VPATH . '/Conf/');
define('CORE_PATH', CLHAPP_PATH . '/Core/');
define('COMMON_PATH', CLHAPP_PATH . '/Common/');

include COMMON_PATH . 'functions.php';
//初始化配置
C(include CONF_PATH . 'conf.php');
//测试环境
$testFile = VPATH . '/test';
if (is_file($testFile)) {
    C(include CONF_PATH . 'test.php');
}

include CORE_PATH . '/Clhapi.class.php';
include CORE_PATH . '/Cache.class.php';
include CORE_PATH . '/Db.class.php';
include CORE_PATH . '/Exception.class.php';
include CORE_PATH . '/Log.class.php';
include CORE_PATH . '/View.class.php';