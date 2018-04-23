<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 15:32
 */
namespace Clhapp;

class Model
{
    protected $error = '';
    protected $errno = '';
    protected $_table = '';

    /**
     * getError @desc 获取错误
     *
     * @author wangjian
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * getErrno @desc 获取错误编号
     *
     * @author wangjian
     * @return string
     */
    public function getErrno()
    {
        return $this->errno;
    }
}