<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/2
 * Time: 21:54
 */

namespace Clhapp\Home\Ajax\Common;

use Clhapp\Api\CookieApi;

class NotifyAjax extends CookieApi
{
    /**
     * run @desc 处理回调接口
     *
     * @author wangjian
     */
    public function run()
    {
        //$this->success();
        print_r('NotifyAjax');
        exit;
    }
}