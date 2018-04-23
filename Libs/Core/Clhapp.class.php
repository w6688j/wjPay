<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 15:34
 */

namespace Clhapp;

class Clhapp
{
    /**
     * start @desc 启动
     *
     * @author wangjian
     * @throws AppException
     */
    static public function start()
    {
        self::init();
        set_exception_handler('Clhapp\Clhapp::appException');
        register_shutdown_function('Clhapp\Clhapp::fatalError');
        set_error_handler('Clhapp\Clhapp::appError');

        if (!defined('APP_PATH')) {
            throw new AppException('项目根目录配置不存在');
        }
        if (!defined('MODEL_PATH')) {
            if (C('MODEL_PATH')) {
                define('MODEL_PATH', APP_PATH . '/' . C('MODEL_PATH'));
            } else {
                throw new AppException('项目模型目录配置不存在');
            }
        }
        if (is_file(APP_PATH . '/Common/functions.php')) {
            include APP_PATH . '/Common/functions.php';
        }

        if (isset($_SERVER['argv']) && IS_CLI) {
            unset($_SERVER['argv'][0]);
            foreach ($_SERVER['argv'] as $k1 => $v1) {
                $str_array = [];
                parse_str($v1, $str_array);
                if (count($str_array) > 1) {
                    $key     = key($str_array);
                    $current = current($str_array);
                    array_shift($str_array);
                    $str_array[$key] = $current . "&" . http_build_query($str_array);
                    $_GET            = $str_array + $_GET;
                    unset($str_array);
                } else {
                    $_GET = $str_array + $_GET;
                }
            }
            $_REQUEST = $_GET;
        }

        Dispatcher::dispatch();
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
        if (!headers_sent()) {
            header('Request-Id:' . SESSION_ID);
            $_SERVER['REQUEST_ID'] = SESSION_ID;
        }
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        ini_set('display_errors', APP_DEBUG);
        //每次发布的版本号
        if (!APP_DEBUG) {
            is_file(VPATH . '/.version') && C('VERSION', file_get_contents(VPATH . '/.version'));
        }
    }

    /**
     * 自定义异常处理
     *
     * @access public
     *
     * @param \Exception $e 异常对象
     */
    static public function appException($e)
    {
        $arr = array_merge([$e->getCode() . ':' . $e->getMessage()], explode("\n", $e->getTraceAsString()));
        Log::write($arr, Log::ERR);

        self::halt($e->getMessage());
    }

    /**
     * fatalError @desc 致命错误捕获
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
                    self::halt(var_export($e, true));
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
     * @param int    $errline 错误行数
     *
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
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
     * @param mixed $e 错误
     */
    static public function halt($e)
    {

        if (APP_DEBUG || IS_CLI) {
            $e = [
                'title'  => $e,
                'sql'    => DB::getError(),
                'get'    => $_GET,
                'post'   => $_POST,
                'server' => $_SERVER,
            ];
            if (IS_CLI) {
                exit(iconv('UTF-8', 'gbk', $e['message']) . PHP_EOL . 'FILE: ' . $e['file'] . '(' . $e['line'] . ')' . PHP_EOL . $e['trace']);
            } else {
                include CLHAPP_PATH . '/Tpl/clhapp_exception.tpl';
                exit;
            }
        } else {
            $error_page = C('ERROR_PAGE');

            if (!empty($error_page)) {
                redirect($error_page);
                exit();
            } else {
                // 发送404信息
                header('HTTP/1.1 404 Not Found');
                header('Status:404 Not Found');
                exit();
            }
        }
    }
}