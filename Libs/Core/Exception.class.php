<?php

namespace Clhapp;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 22:10
 */
class AppException extends \Exception
{
    protected $errno;

    /**
     * AppException constructor. 构造函数
     *
     * @param string $message 错误信息
     * @param int    $code    错误码
     */
    public function __construct($message, $code = 0)
    {
        if (is_array($message)) {
            list($msg, $errno) = $message;
            parent::__construct($msg, $code);
            $this->errno = $errno ?: $code;
        } else {
            $this->errno = (string)$code;
            parent::__construct($message, (int)$code);
        }

    }

    /**
     * getError @desc 获取错误
     *
     * @author wangjian
     * @return string
     */
    public function getError()
    {
        return $this->getMessage();
    }

    /**
     * getErrorInfo @desc 获取错误信息
     *
     * @author wangjian
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->getCode() . ':' . $this->getMessage();
    }

    /**
     * getErrno @desc 获取错误码
     *
     * @author wangjian
     * @return string
     */
    public function getErrno()
    {
        return $this->errno;
    }
}