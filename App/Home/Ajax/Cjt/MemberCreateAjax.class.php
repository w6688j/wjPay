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

class MemberCreateAjax extends CookieApi
{
    /**
     * run @desc 销售订单新增接口
     *
     * @author wangjian
     */
    public function run()
    {
        $rst = DealCjt::memberCreate([
            'Code'        => 'PD402',
            'CardCode'    => 'PD402',
            'Name'        => 'wj',
            'MemberType'  => ['Code' => '1585888'],
            'Mobilephone' => '18817834950',
        ]);

        print_r($rst);
    }
}