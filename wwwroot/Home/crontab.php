<?php
require_once dirname(dirname(__DIR__)) . '/Libs/Clhapp.php';
define('APP_PATH', VPATH . '/App/Home');
$_GET['m'] = 'crontab';
$env       = 'ONLINE';
if (file_exists(VPATH . '/.host')) {
    $env = $_SERVER['HTTP_HOST'] = file_get_contents(VPATH . '/.host');
}
define('ENV', $env);
Clhapp\Clhapp::start();