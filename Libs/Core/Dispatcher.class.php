<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 15:32
 */
namespace Clhapp;

class Dispatcher
{
    /**
     * dispatch @desc 路径处理
     *
     * @author wangjian
     * @throws AppException
     */
    static public function dispatch()
    {
        $Model  = isset($_GET[C('MODULE_LAYER')]) && $_GET[C('MODULE_LAYER')]
            ? $_GET[C('MODULE_LAYER')] : C('DEFAULT_MODULE');
        $Action = isset($_GET[C('ACTION_LAYER')]) && $_GET[C('ACTION_LAYER')]
            ? $_GET[C('ACTION_LAYER')] : C('DEFAULT_ACTION');

        if ($Model) {
            $Model = strtolower($Model);
            define('MODULE_NAME', $Model);
            define('ACTION_NAME', $Action);
            if (!preg_match("#^[\w]+$#", $Model)) {
                if (APP_DEBUG) {
                    throw new AppException('非法请求');
                } else {
                    die();
                }
            }
            $ModelName = ucfirst($Model) . 'Action';
            $ModelFile = APP_PATH . "/Action/{$ModelName}.class.php";

            if (is_file($ModelFile)) {
                include $ModelFile;
                $dirname   = basename(APP_PATH);
                $ModelName = 'Clhapp\\' . $dirname . '\\Action\\' . $ModelName;
                $ModelObj  = new $ModelName();
                if (method_exists($ModelObj, $Action)) {
                    if (substr($Action, 0, 4) === 'ajax') {
                        if (!IS_AJAX && !APP_DEBUG) {
                            api_return([
                                'code' => 500,
                                'msg'  => '非ajax请求',
                            ]);
                            die();
                        }
                    }
                    $ModelObj->$Action();
                } elseif ($Action == 'list' && method_exists($ModelObj, '_list')) {
                    $ModelObj->_list();
                } elseif (method_exists($ModelObj, '_empty')) {
                    $ModelObj->_empty();
                } else {
                    if (APP_DEBUG) {
                        throw new AppException($ModelName . '不存在操作' . $Action);
                    }
                }
            } else {
                if (APP_DEBUG) {
                    throw new AppException('模块MODEL:' . $Model . '不存在');
                }
            }
        }
    }
}