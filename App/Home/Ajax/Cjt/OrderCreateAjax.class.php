<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 11:16
 */

namespace Clhapp\Home\Ajax\Cjt;

use Clhapp\Api\CookieApi;
use Clhapp\Cjt\DealCjt;

class OrderCreateAjax extends CookieApi
{
    /**
     * run @desc 畅捷通采购订单接口
     *
     * @author wangjian
     */
    public function run()
    {
        $rst = DealCjt::orderCreate([
            'ExternalCode'          => '111',
            'Code'                  => '1',
            'VoucherDate'           => '2016-05-20',
            'VoucherDetails'        => [],
            'BusiType'              => ['code' => '01'],
            'Partner'               => ['code' => '001'],
            'Warehouse'             => ['code' => '001'],
            'Inventory'             => ['code' => '0010'],
            'BaseQuantity'          => ['code' => '21'],
            'Memo'                  => '',
            'Department'            => '',
            'Clerk'                 => '',
            'DynamicPropertyKeys'   => [],
            'DynamicPropertyValues' => [],
            'Project'               => '',
            'ExpiryDate'            => '',
            'Batch'                 => '',
            'Amount'                => '0.1',
            'Price'                 => '0.1',
            'SubQuantity'           => [],
        ]);

        print_r($rst);
        exit;
    }
}