<?php
namespace Clhapp;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:10
 */
use Clhapp\Mail\sendcloud;

/**
 * @desc  日志处理类
 * @author: wangjian
 * @since :  14-8-9 下午4:58
 */
class Log
{
    //日志方式
    const FILE = 1;
    const DATABASE = 2;
    //日志等级
    const DEBUG = 'DEBUG';
    const ERR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';
    const SQL = 'SQL';
    const MEMCACHE = 'MEMCACHE';
    //日志颜色
    const COLOR_RIGHT = "\033[1;40;35m";
    const COLOR_ERROR = "\033[1;40;31m";
    const COLOR_COMMON = "\033[1;40;33m";
    const COLOR_GREEN = "\033[40;32m";
    const COLOR_BLUE = "\033[1;40;36m";
    const COLOR_GREY = "\033[1;40;30m";
    const COLOR_END = "\033[0m";
    // 日志信息
    static $log = [];
    // 日期格式
    static $format = '[Y-m-d H:i:s]';

    //日志开关
    static $status
        = [
            self::DEBUG,
            self::NOTICE,
            self::ERR,
            self::WARNING,
            self::SQL,
        ];
    //日志记录方式
    static $recordtype = self::FILE;

    /**
     * record @desc 记录日志 并且会过滤未经设置的级别
     *
     * @author wangjian
     *
     * @param string $message 日志信息
     * @param string $level   日志等级
     */
    static function record($message, $level = self::DEBUG)
    {
        if (C('LOG_STATUS') === false || !APP_DEBUG) {
            return;
        }
        if (C('LOG_WRITE')) {
            self::write($message, $level);

            return;
        }
        $message     = self::formatMessage($message);
        $level       = self::COLOR_BLUE . $level . self::COLOR_END;
        self::$log[] = date(self::$format) . "\t{$level}\t{$message}";
    }

    /**
     * save @desc 日志保存
     *
     * @author wangjian
     */
    static public function save()
    {
        if (C('LOG_TRACE')) {
            $traceLog = [];
            foreach (utf8togbk(trace()) as $k => $v) {
                $traceLog[] = "[TRACE:{$k}]\n" . implode("\n", $v);
            }
            self::$log = array_merge(self::$log, $traceLog);
        }
        if (empty(self::$log)) {
            return;
        }
        $destination = C('LOG_PATH');
        $start       = self::COLOR_RIGHT . "\r\n[LOG_RECORD_START]\r\n" . self::COLOR_END;
        $end         = self::COLOR_RIGHT . "\r\n[LOG_RECOED_END]\r\n" . self::COLOR_END;

        $pre = $start . date(self::$format) . self::COLOR_COMMON . ' [IP:' . $_SERVER['REMOTE_ADDR'] . ']';
        if (!IS_CLI) {
            $pre .= "\t" . __URL__ . self::COLOR_END . "\r\n";
        } else {
            $pre .= self::COLOR_END . "\r\n";
        }

        error_log($pre . implode("\r\n", self::$log) . $end, 3, $destination);
        // 保存后清空日志缓存
        self::$log = [];
    }

    /**
     * sendEmail @desc 发送邮件通知
     *
     * @author wangjian
     *
     * @param string $message 错误内容
     */
    static private function sendEmail($message)
    {
        //每小时发送一次
        $key = 'sendcloud_' . date('YmdHm');
        if (!McacheFactory::provide()->get($key)) {
            try {
                $data    = [
                    'title'  => gbktoutf8($message),
                    'sql'    => DB::getError(),
                    'error'  => var_export(error_get_last(), true),
                    'traces' => getDebugTrace(),
                    'get'    => $_GET,
                    'post'   => $_POST,
                    'server' => $_SERVER,
                ];
                $viewObj = new View();
                $viewObj->assign('data', $data);
                $html = $viewObj->display(CLHAPP_PATH . '/Tpl/mail.tpl.html', false);

                $obj = new sendcloud();
                $obj->setSubject('异常错误报告');
                $obj->setContent($html);
                $obj->setAddress('953372680@qq.com');
                $obj->send2();

            } catch (AppException $e) {

            }
            McacheFactory::provide()->set($key, 1);
        }
    }

    /**
     * write @desc 日志直接写入
     *
     * @author wangjian
     *
     * @param string $message     日志信息
     * @param string $level       日志等级
     * @param null   $destination 日志位置
     */
    static function write($message, $level = self::DEBUG, $destination = null)
    {
        $message = self::formatMessage($message);
        if (!APP_DEBUG && $level == self::ERR) {
            self::sendEmail($message);
        }
        $now = date(self::$format);
        if ($destination === null) {
            if ($level === self::ERR) {
                $destination = C('LOG_PATH_ERROR');
            } elseif ($level === self::SQL) {
                $destination = C('LOG_PATH_SQL');
            } else {
                $destination = C('LOG_PATH');
            }
        }
        if (!$destination && APP_DEBUG) {
            $destination = C('LOG_PATH');
        }

        $level = self::COLOR_RIGHT . $level . self::COLOR_END;
        if (checkUTF8($level)) {
            $level = utf8togbk($level);
        }
        if (APP_DEBUG && IS_CLI) {
            echo "{$now}\t{$level}\t\n{$message}\r\n";

            return;
        }
        error_log(
            self::COLOR_BLUE . "{$now} " . "IP:{$_SERVER['REMOTE_ADDR']} " . self::COLOR_END . self::COLOR_GREY . SESSION_ID . self::COLOR_END
            . "\t{$level}\t{$message}\r\n", 3, $destination
        );
    }

    /**
     * formatMessage @desc 格式化日志消息
     *
     * @author wangjian
     *
     * @param mixed $message 消息
     *
     * @return mixed
     */
    private static function formatMessage($message)
    {
        //日志对齐
        if (checkUTF8(serialize($message))) {
            $message = utf8togbk($message);
        }

        return print_r($message, true);
    }
}