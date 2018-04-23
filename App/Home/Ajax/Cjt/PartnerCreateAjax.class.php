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

class PartnerCreateAjax extends CookieApi
{
    /**
     * run @desc 销售订单新增接口
     *
     * @author wangjian
     */
    public function run()
    {
        $rst = DealCjt::partnerCreate([
            'Code'                 => 'IZHUYAN01',
            'Name'                 => '竹燕科技A0911111111111',
            'PartnerType'          => ['Code' => '01'],
            'PartnerClass'         => ['Code' => '931849'],
            'CustomerAddressPhone' => '16600000003',
            'Disabled'             => 'False',
            'PartnerAddresDTOs'    => [
                [
                    'Code'        => '001',
                    'Name'        => '长安街',
                    'Contact'     => 'wj',
                    'MobilePhone' => '16600000003',
                    'Fax'         => '16600000003',
                    'EmailAddr'   => '16600000003@qq.com',
                    'QqNo'        => '16600000003',
                ],
            ],
        ]);

        print_r($rst);
    }
}