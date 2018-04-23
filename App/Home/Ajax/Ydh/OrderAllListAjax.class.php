<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/12
 * Time: 23:57
 */

namespace Clhapp\Home\Ajax\Ydh;

use Clhapp\Api\CookieApi;
use Clhapp\Ydh\DealYdh;

class OrderAllListAjax extends CookieApi
{
    /**
     * run @desc 订单列表接口
     *
     * @author wangjian
     */
    public function run()
    {
        $list = DealYdh::orderAllList(['status' => '6']);

        $this->success($list);
    }
}