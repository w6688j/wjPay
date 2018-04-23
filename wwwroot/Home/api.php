<?php
require_once dirname(dirname(__DIR__)) . '/Libs/Api.php';
define('APP_PATH', VPATH . '/App/Home');
try {
    Clhapp\Clhapi::start();
} catch (\Clhapp\AppException $e) {
    print_r($e);
    die();
}