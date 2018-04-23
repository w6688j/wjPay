<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2017/2/5
 * Time: 11:45
 */

namespace crontab;

interface Logger
{
    /**
     * @desc   日志输出
     * @author wangjian
     *
     * @param string $log 日志内容
     *
     * @return void
     */
    public function write($log);
}