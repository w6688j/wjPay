<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/26
 * Time: 0:34
 */

namespace Clhapp\Home\Ajax\Ydh;

use Clhapp\Api\CookieApi;
use Clhapp\Ydh\DealYdh;

class GoodListAjax extends CookieApi
{
    /**
     * run @desc 商品列表
     *
     * @author wangjian
     */
    public function run()
    {
        $list = DealYdh::goodList();

        $this->success($list);
    }
}