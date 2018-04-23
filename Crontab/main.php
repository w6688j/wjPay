<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2017/2/21
 * Time: 23:06
 */
define('VPATH', dirname(__DIR__));
require VPATH . '/Crontab/Lib/CronMain.php';
require VPATH . '/Crontab/Lib/CronTask.php';
require VPATH . '/Crontab/Lib/CronTime.php';
require VPATH . '/Crontab/Lib/Logger.php';
require VPATH . '/Crontab/Lib/Task.php';
require VPATH . '/Crontab/Lib/log/CronLog.php';
require VPATH . '/Crontab/Lib/CronEmail.php';
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
ini_set('date.timezone', 'PRC');
ini_set('display_errors', true);
$env = 'ONLINE';
if (file_exists(VPATH . '/.host')) {
    $env = file_get_contents(VPATH . '/.host');
}
define('ENV', $env);
switch (ENV) {
    case 'ONLINE':
        $confFile = VPATH . '/Crontab/Conf/conf.php';
        break;
    default:
        $confFile = VPATH . '/Crontab/Conf/test.php';
}
$main = new \crontab\CronMain($confFile);
$main->start();


