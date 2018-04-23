<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/26
 * Time: 23:10
 */

namespace Clhapp\Sync;

use Clhapp\AppException;
use Clhapp\Cjt\DealCjt;
use Clhapp\Log;
use Clhapp\Ydh\DealYdh;
use Clhapp\Ydh\YdhApi;
use Clhapp\Ydh\YdhOrderApi;

class CjtSync
{
    /**
     * syncSaleOrder @desc 同步畅捷通销售订单
     *
     * @author wangjian
     */
    static public function syncSaleOrder()
    {
        set_time_limit(0);
        $currentPage = 1;
        while (true) {
            $list = DealYdh::orderAllList([], $currentPage);
            foreach ($list['items'] as $k => $v) {
                if ($v['status'] != YdhOrderApi::STATUS_SUBMIT && strtotime($v['modifyTime']) > strtotime(date('Y-m-d') . ' 00:00:00')) {
                    try {
                        DealCjt::saleOrderCreate(self::parseSaleOrderData($v));
                    } catch (AppException $e) {
                        Log::write($e);
                        continue;
                    }
                }
                if (strtotime($v['modifyTime']) < strtotime(date('Y-m-d') . ' 00:00:00')) {
                    break 2;
                }
            }
            if ($currentPage > 50) {
                break;
            }
            if (count($list['items']) < YdhApi::LIST_SIZE) {
                break;
            }

            $currentPage++;
        }
    }

    /**
     * parseSaleOrderData @desc 解析销售订单数据
     *
     * @author wangjian
     *
     * @param array $order 订单数据
     *
     * @return array
     */
    static protected function parseSaleOrderData($order = [])
    {
        $data = [
            'VoucherDate'      => date('Y-m-d'),
            'Code'             => $order['orderNum'] ? $order['orderNum'] : '',
            'ExternalCode'     => $order['orderNum'] ? $order['orderNum'] . '01' : '',
            'Customer'         => ['Code' => $order['customer']['code']],
            'Warehouse'        => $order['customer']['warehouseId'] > 0 ? ['code' => $order['customer']['warehouseId']] : ['code' => '252604'],
            'DeliveryDate'     => $order['deliveryDate'] ? $order['deliveryDate'] : date('Y-m-d', strtotime('+1 day', time())),
            'Address'          => $order['addressLabel'] ? $order['addressLabel'] : '',
            'LinkMan'          => $order['customer']['name'] ? $order['customer']['name'] : '',
            'ContactPhone'     => $order['mobile'] ? $order['mobile'] : '',
            'Memo'             => $order['orderNum'] ? $order['orderNum'] : '',
            'Member'           => ['MemberType' => ['Code' => $order['customerType']['id']]],
            'SaleOrderDetails' => [],
        ];

        if ($order['details']) {
            foreach ($order['details'] as $k => $v) {
                array_push($data['SaleOrderDetails'], [
                    'Inventory'             => ['Code' => $v['productCode']],
                    'Unit'                  => ['Name' => $v['mainUnitName']],
                    'Quantity'              => (int)$v['count'] ? $v['count'] : 1,
                    'IsPresent'             => "false",
                    'TaxRate'               => 0,
                    'OrigPrice'             => $v['price'] && $v['count'] ? $v['price'] * $v['count'] : 0,
                    'OrigDiscountPrice'     => (float)$v['originPrice'] ? $v['originPrice'] : 0,
                    'OrigDiscountAmount'    => (float)$v['money'] ? $v['money'] : 0,
                    'DynamicPropertyKeys'   => ['pubuserdefnvc1'],
                    'DynamicPropertyValues' => [false],
                ]);
            }
        }

        return $data;
    }

    /**
     * syncMember @desc 同步畅捷通会员
     *
     * @author wangjian
     */
    static public function syncMember()
    {
        set_time_limit(0);
        $currentPage = 1;
        while (true) {
            $list = DealYdh::customerList($currentPage);
            foreach ($list['items'] as $k => $v) {
                //同步今天的客户
                if ($v['customerStatus'] == 0 && strtotime($v['createTime']) > strtotime(date('Y-m-d') . ' 00:00:00')) {
                    $rst = DealCjt::memberCreate(self::parseMember($v));
                    Log::write(round(memory_get_usage(true) / 1048576, 6), 'memory');
                    Log::write($rst, '$rst');
                }
            }
            if (count($list['items']) < YdhApi::LIST_SIZE) {
                break;
            }
            $currentPage++;
        }
    }

    /**
     * parseMember @desc 解析销售会员数据
     *
     * @author wangjian
     *
     * @param array $member 会员数据
     *
     * @return array
     */
    static public function parseMember($member = [])
    {
        return [
            'Code'        => $member['code'],
            'CardCode'    => $member['code'],
            'Name'        => $member['realName'],
            'MemberType'  => ['Code' => $member['customertypeId']],
            'Mobilephone' => $member['mobile'],
        ];
    }

    /**
     * syncPartner @desc 同步客户数据
     *
     * @author wangjian
     */
    static public function syncPartner()
    {
        set_time_limit(0);
        $currentPage = 1;
        while (true) {
            $list = DealYdh::customerList($currentPage);
            foreach ($list['items'] as $k => $v) {
                //同步今天的客户
                if ($v['customerStatus'] == 0 && strtotime($v['createTime']) > strtotime(date('Y-m-d') . ' 00:00:00')) {
                    $rst = DealCjt::partnerCreate(self::parsePartner($v));
                    Log::write(round(memory_get_usage(true) / 1048576, 6), 'memory');
                    Log::write($rst, '$rst');
                }
            }
            if (count($list['items']) < YdhApi::LIST_SIZE) {
                break;
            }
            $currentPage++;
        }
    }

    /**
     * parsePartner @desc 解析客户数据
     *
     * @author wangjian
     *
     * @param array $partner 客户数据
     *
     * @return array
     */
    static public function parsePartner($partner = [])
    {
        return [
            'Code'                 => $partner['code'],
            'Name'                 => $partner['name'],
            'PartnerType'          => ['Code' => '01'],
            'PartnerClass'         => ['Code' => $partner['customerType']['id']],
            'CustomerAddressPhone' => $partner['mobile'],
            'Disabled'             => 'False',
            'PartnerAddresDTOs'    => [
                [
                    'Code'        => $partner['id'] . $partner['customRegionId'],
                    'Name'        => $partner['area'] . $partner['address'],
                    'Contact'     => $partner['contactor'],
                    'MobilePhone' => $partner['mobile'],
                    'Fax'         => $partner['fax'],
                    'EmailAddr'   => $partner['email'],
                    'QqNo'        => $partner['qq'],
                ],
            ],
        ];
    }

    /**
     * syncInventory @desc 同步存货数据
     *
     * @author wangjian
     */
    static public function syncInventory()
    {
        set_time_limit(0);
        $currentPage = 1;
        while (true) {
            $list = DealYdh::goodList($currentPage);
            foreach ($list['items'] as $k => $v) {
                //同步今天的客户
                DealCjt::InventoryCreate(self::parseInventory($v));
                Log::write(round(memory_get_usage(true) / 1048576, 6), 'memory');
            }
            if (count($list['items']) < YdhApi::LIST_SIZE) {
                break;
            }
            $currentPage++;
        }
    }

    /**
     * parseInventory @desc
     *
     * @author wangjian
     *
     * @param $inventory
     *
     * @return array
     */
    static protected function parseInventory($inventory = [])
    {
        $data = [
            'Code'           => $inventory['code'],
            'Name'           => $inventory['name'],
            'Shorthand'      => $inventory['name'],
            'Specification'  => '1g',
            'DefaultBarCode' => $inventory['code'],
            'InventoryClass' => ['Code' => $inventory['productType']['id'], 'Name' => $inventory['productType']['name']],
            'ProductInfo'    => ['Code' => '01', 'Name' => '-'],
            'Unit'           => ['Code' => '2', 'Name' => '个'],
            'IsPurchase'     => 'True',
            'IsSale'         => 'True',
            'IsMadeSelf'     => 'False',
            'IsMaterial'     => 'False',
            'IsSuite'        => 'False',
            'IsLaborCost'    => 'False',
        ];

        //$inventory['productUnitId'] && $data['Unit'] = ['Code' => $inventory['productUnitId'], 'Name' => $inventory['productUnitName']];

        return $data;
    }
}