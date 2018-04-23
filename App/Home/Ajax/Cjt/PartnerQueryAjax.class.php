<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 18:54
 */

namespace Clhapp\Home\Ajax\Cjt;

use Clhapp\Api\CookieApi;
use Clhapp\Cjt\DealCjt;
use Clhapp\Sync\CjtSync;

class PartnerQueryAjax extends CookieApi
{
    /**
     * run @desc 销售订单新增接口
     *
     * @author wangjian
     */
    public function run()
    {
        $list = DealCjt::partnerQuery();

        $this->success($list);
    }
}