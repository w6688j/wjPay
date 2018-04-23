<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 0:15
 */

namespace Clhapp\Home\Ajax\Ydh;

use Clhapp\Api\CookieApi;
use Clhapp\Ydh\DealYdh;

class CustomerListAjax extends CookieApi
{
    /**
     * run @desc 获取客户类型列表
     *
     * @author wangjian
     */
    public function run()
    {
        $list = DealYdh::customerList();

        $this->success($list);
    }
}