<?php
require_once dirname(dirname(__DIR__)) . '/Libs/Clhapp.php';
define('APP_PATH', VPATH . '/App/Home');
try {
    Clhapp\Clhapp::start();
} catch (\Clhapp\AppException $e) {
    print_r($e);
    die();
}