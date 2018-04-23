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

class SaleOrderCreateAjax extends CookieApi
{
    /**
     * run @desc 销售订单新增接口
     *
     * @author wangjian
     */
    public function run()
    {
        $rst = DealCjt::saleOrderCreate([
            'VoucherDate'      => date('Y-m-d'),
            'ExternalCode'     => 'SO20170350001' . rand(10, 99),
            'ReciveType'       => ['Code' => '05'],
            'Code'             => 'DH-O-20170425-018870' . rand(10, 99),
            'Customer'         => ['Code' => '8490001'],
            'Warehouse'        => ['code' => '001'],
            'DeliveryDate'     => '2017-05-31',
            'Address'          => '地址',
            'LinkMan'          => '联系人',
            'ContactPhone'     => '13611111111',
            'Memo'             => '测试OpenAPI',
            'Member'           => ['MemberType' => ['Code' => '']],
            'SaleOrderDetails' => [
                [
                    'Inventory'             => ['Code' => '1234556'],
                    'Unit'                  => ['Name' => '支'],
                    'Quantity'              => 7104,
                    'IsPresent'             => "false",
                    'TaxRate'               => 0,
                    'OrigDiscountPrice'     => 14.43,
                    'OrigDiscountAmount'    => 102510.72,
                    'DynamicPropertyKeys'   => ['pubuserdefnvc1'],
                    'DynamicPropertyValues' => [false],
                ],
            ],
        ]);

        print_r($rst);
    }
}