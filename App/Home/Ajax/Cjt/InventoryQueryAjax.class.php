<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/21
 * Time: 0:57
 */

namespace Clhapp\Home\Ajax\Cjt;

use Clhapp\Api\CookieApi;
use Clhapp\Cjt\DealCjt;

class InventoryQueryAjax extends CookieApi
{
    /**
     * run @desc 畅捷通存货查询接口
     *
     * @author wangjian
     */
    public function run()
    {
        $list = DealCjt::inventoryQuery();

        $this->success($list);
    }
}