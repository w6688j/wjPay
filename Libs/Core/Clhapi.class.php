<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:10
 */

namespace Clhapp;

class Clhapi
{
    /**
     * start @desc 入口
     *
     * @author wangjian
     * @throws AppException
     */
    static public function start()
    {
        self::init();
        set_exception_handler('Clhapp\Clhapi::appException');
        register_shutdown_function('Clhapp\Clhapi::fatalError');
        set_error_handler('Clhapp\Clhapi::appError');

        if (!defined('APP_PATH')) {
            throw new AppException('项目根目录配置不存在', 929001);
        }

        if (is_file(APP_PATH . '/Common/functions.php')) {
            include APP_PATH . '/Common/functions.php';
        }
        self::dispatch();
    }

    /**
     * dispatch @desc 地址解析
     *
     * @author wangjian
     * @throws AppException
     */
    static private function dispatch()
    {
        $Model  = isset($_GET[C('MODULE_LAYER')]) && $_GET[C('MODULE_LAYER')]
            ? $_GET[C('MODULE_LAYER')]
            : C('DEFAULT_MODULE');
        $Action = isset($_GET[C('ACTION_LAYER')]) && $_GET[C('ACTION_LAYER')]
            ? $_GET[C('ACTION_LAYER')]
            : C('DEFAULT_ACTION');
        if ($Model) {
            $Model = strtolower($Model);
            define('MODULE_NAME', $Model);
            define('ACTION_NAME', $Action);
            if (!preg_match("#^[\w]+$#", $Model)) {
                    throw new AppException('非法请求', 929010);
            }
            $ModelName  = ucfirst($Model);
            $ActionName = ucfirst($Action) . 'Ajax';
            $ModelFile  = APP_PATH . "/Ajax/{$ModelName}/{$ActionName}" . EXT;

            if (is_file($ModelFile)) {
                include $ModelFile;
                $dirname   = basename(APP_PATH);
                $ModelName = 'Clhapp\\' . $dirname . '\\Ajax\\' . $ModelName . '\\' . $ActionName;
                if (class_exists($ModelName)) {
                    $ModelObj = new $ModelName();
                    method_exists($ModelObj, 'run') && $ModelObj->run();
                } else {
                    if (APP_DEBUG) {
                        throw new AppException($ModelName . ' 该类不合法~', 929020);
                    }
                }
            } else {
                if (APP_DEBUG) {
                    throw new AppException('接口文件:' . $ModelFile . '不存在', 929030);
                }
            }
        }
    }

    /**
     * init @desc 初始化
     *
     * @author wangjian
     */
    static private function init()
    {
        $webConfig = C('HOST_CONFIG');
        if (isset($webConfig[$_SERVER['HTTP_HOST']])) {
            $conf_path = APP_PATH . '/Conf/' . $webConfig[$_SERVER['HTTP_HOST']] . '.php';
            file_exists($conf_path) && C(include $conf_path);
        } elseif (isset($webConfig['default'])) {
            $conf_path = APP_PATH . '/Conf/' . $webConfig['default'] . '.php';
            file_exists($conf_path) && C(include $conf_path);
        }

        if (C('DEBUG')) {
            define('APP_DEBUG', true);
        } else {
            define('APP_DEBUG', false);
        }

        define('SESSION_ID', uniqid());

        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        ini_set('display_errors', APP_DEBUG);
        if (!headers_sent()) {
            header('Request-Id:' . SESSION_ID);
            $_SERVER['REQUEST_ID'] = SESSION_ID;
        }
        //每次发布的版本号
        if (!APP_DEBUG) {
            is_file(VPATH . '/.version') && C('VERSION', file_get_contents(VPATH . '/.version'));
        }
    }

    /**
     * appException @desc 自定义异常处理
     *
     * @author wangjian
     *
     * @param \Exception $e 异常对象
     */
    static public function appException($e)
    {
        $error            = [];
        $error['message'] = $e->getMessage();
        $trace            = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();
        Log::write($error['message'], Log::ERR);
        // 发送404信息
        self::halt($e);
    }

    /**
     * fatalError @desc 捕获致命错误
     *
     * @author wangjian
     */
    static public function fatalError()
    {
        Log::write(DB::$sql_count, 'DB EXEC');
        Log::save();
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    Log::write($e, Log::ERR);
                    self::halt($e);
                    break;
            }
        }
    }

    /**
     * appError @desc 自定义错误处理
     *
     * @author wangjian
     *
     * @param int    $errno   错误类型
     * @param string $errstr  错误信息
     * @param string $errfile 错误文件
     * @param string $errline 错误行数
     */
    static public function appError($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
                self::halt($errorStr);
                break;
            case E_NOTICE:
                break;
            default:
                $errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
                Log::write($errorStr, 'APP-ERROR');
                break;
        }
    }

    /**
     * halt @desc 错误输出
     *
     * @author wangjian
     *
     * @param mixed $error 错误
     */
    static public function halt($error)
    {
        $e = [];
        if (APP_DEBUG) {
            //调试模式下输出错误信息
            ob_start();
            if (!is_array($error)) {
                $trace                = debug_backtrace();
                $e['message']         = $error;
                $e['file']            = $trace[0]['file'];
                $e['line']            = $trace[0]['line'];
                $e['debug_backtrace'] = $trace;
                debug_print_backtrace();
                $e['trace'] = ob_get_clean();

            } else {
                $e = $error;
                debug_print_backtrace();
                $e['trace'] = ob_get_clean();
            }
            api_return([
                'code' => 929050,
                'msg'  => '服务器异常',
                'data' => $e,
            ]);

        } else {
            api_return([
                'code' => 929050,
                'msg'  => '系统异常',
            ]);
        }
    }

}