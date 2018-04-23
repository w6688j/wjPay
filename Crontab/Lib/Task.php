<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2017/2/5
 * Time: 16:24
 */

namespace crontab;

interface Task
{
    /**
     * @desc   run
     * @author wangjian
     *
     * @param array $args 参数
     *
     * @return void
     */
    public function run($args);
}