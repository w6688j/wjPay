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

class CustomerTypeListAjax extends CookieApi
{
    /**
     * run @desc 获取客户类型列表
     *
     * @author wangjian
     */
    public function run()
    {
        $list = DealYdh::customerTypeList();

        $this->success($list);
        exit;
    }
}