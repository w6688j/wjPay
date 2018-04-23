<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 23:43
 */

namespace Clhapp\Sync;

use Clhapp\AppException;
use Clhapp\Cjt\CjtApi;
use Clhapp\Cjt\DealCjt;
use Clhapp\Log;
use Clhapp\Ydh\DealYdh;
use Clhapp\Ydh\YdhOrderApi;

class YdhSync
{
    /**
     * syncGood @desc 同步易订货商品
     *
     * @author wangjian
     */
    static public function syncGood()
    {
        set_time_limit(0);
        $Ts = '';
        while (true) {
            $list = DealCjt::inventoryQuery($Ts);
            foreach ($list as $k => $v) {
                $Ts = $v['Ts'];
                try {
                    if ($v['IsSale'] == 'True') {
                        if (self::ydhHasGood($v, $goodId, $marketPrice, $orderPrice)) {
                            $list[$k]['id']          = $goodId;
                            $list[$k]['marketPrice'] = $marketPrice;
                            $list[$k]['orderPrice']  = $orderPrice;
                        }
                        try {
                            DealYdh::goodCreate(self::parseGoodData($list[$k]));
                        } catch (AppException $e) {
                            Log::write($e);
                        }
                        $goodInventoryData = self::parseGoodInventoryData($list[$k]);
                        if ($goodInventoryData['goodsCode']) {
                            DealYdh::goodInventory($goodInventoryData);
                        }
                    }
                } catch (AppException $e) {
                    Log::write($e);
                    continue;
                }
            }
            if (count($list) < CjtApi::LIST_SIZE) {
                break;
            }
        }
    }

    /**
     * ydhHasGood @desc 易订货是否已经存在此商品
     *
     * @author wangjian
     *
     * @param array $good        畅捷通商品数据
     * @param int   $goodId      易订货商品id
     * @param float $marketPrice 易订货商品市场价
     * @param float $orderPrice  易订货商品订货价
     *
     * @return bool 存在返回true
     */
    static protected function ydhHasGood($good = [], &$goodId = 0, &$marketPrice = 0.0, &$orderPrice = 0.0)
    {
        $ydhGoods = DealYdh::goodList();
        if ($ydhGoods['items']) {
            foreach ($ydhGoods['items'] as $item) {
                if ($good['Code'] == $item['code']) {
                    $goodId      = $item['id'];
                    $marketPrice = $item['marketPrice'];
                    $orderPrice  = $item['orderPrice'];

                    return true;
                    break;
                }
            }
        }

        return false;
    }

    /**
     * parseGoodData @desc 解析存货数据
     *
     * @author wangjian
     *
     * @param array $good 存货数据
     *
     * @return array
     */
    static protected function parseGoodData($good = [])
    {
        $unit = self::getYdhUnit($good);
        $data = [
            'name'            => $good['Name'],
            'code'            => $good['Code'],
            'spec'            => $good['Specification'],
            'productTypeId'   => $good['InventoryClass']['Code'],
            'productUnitId'   => $unit['id'],
            'productUnitName' => $unit['name'],
            'marketPrice'     => $good['InvSCost'] ? round($good['InvSCost'], 2) : 0,
            'orderPrice'      => $good['AvagCost'] ? round($good['AvagCost'], 2) : 0,
            'imgUrl'          => $good['ImageList'][0]['ImageUrl'],
            'barcode'         => $good['DefaultBarCode'],
        ];

        if ($good['id'] > 0) {
            $data['id']          = $good['id'];
            $data['marketPrice'] = $good['marketPrice'];
            $data['orderPrice']  = $good['orderPrice'];
        }

        return $data;
    }

    /**
     * getYdhUnit @desc 根据erp中的商品分类名称获取易订货中的商品分类
     *
     * @author wangjian
     *
     * @param array $good 商品数据
     *
     * @return array
     */
    static protected function getYdhUnit($good = [])
    {
        $arr         = [];
        $ydhUnitList = DealYdh::goodUnitList();
        foreach ($ydhUnitList['items'] as $k => $v) {
            if ($good['Unit']['Name'] == $v['name']) {
                $arr = $v;
                break;
            }
        }

        return $arr;
    }

    /**
     * parseGoodInventoryData @desc 解析存货库存数据
     *
     * @author wangjian
     *
     * @param array $good 存货库存数据
     *
     * @return array
     */
    static protected function parseGoodInventoryData($good = [])
    {
        $currentStockList = DealCjt::currentStockQuery([
            'Warehouse'          => [['Code' => '']],
            'InvBarCode'         => '',
            'BeginInventoryCode' => '',
            'EndInventoryCode'   => '',
            'InventoryName'      => '',
            'Specification'      => '',
            'Brand'              => '',
            'GroupInfo'          => [
                'Warehouse'   => true,
                'Inventory'   => true,
                'Brand'       => true,
                'InvProperty' => true,
            ],
        ]);
        $InventoryCode    = '';
        $inventory        = 0;
        foreach ($currentStockList as $k => $v) {
            if ($v['InventoryCode'] == $good['Code']) {
                $InventoryCode = $v['InventoryCode'];
                $inventory += $v['AvailableQuantity'];
            }
        }

        if ($InventoryCode) {
            return [
                'goodsCode' => (string)$InventoryCode,
                'inventory' => (int)$inventory,
            ];
        } else {
            return [];
        }
    }

    /**
     * syncCustomer @desc 同步客户
     *
     * @author wangjian
     */
    static public function syncCustomer()
    {
        set_time_limit(0);
        $Ts = '';
        while (true) {
            $list = DealCjt::partnerQuery($Ts);
            foreach ($list as $k => $v) {
                $Ts = $v['Ts'];
                try {
                    if ($v['Disabled'] == 'False') {
                        DealYdh::customerCreate(self::parseCustomerData($v));
                    }
                } catch (AppException $e) {
                    Log::write($e);
                    continue;
                }
            }
            if (count($list) < CjtApi::LIST_SIZE) {
                break;
            }
        }
    }

    /**
     * parseCustomerData @desc 解析客户数组
     *
     * @author wangjian
     *
     * @param array $customer 客户数组
     *
     * @return array
     */
    static protected function parseCustomerData($customer = [])
    {
        return [
            'name'           => $customer['Name'],
            'code'           => $customer['Code'],
            'contactor'      => $customer['PartnerAddresDTOs'][0]['Contact'],
            'mobile'         => $customer['PartnerAddresDTOs'][0]['MobilePhone'],
            'address'        => $customer['PartnerAddresDTOs'][0]['ShipmentAddress'],
            'position'       => $customer['PartnerAddresDTOs'][0]['Position'],
            'customertypeId' => $customer['PartnerClass']['Code'],
        ];
    }

    /**
     * syncLogisticsDeliver @desc 同步发货单
     *
     * @author wangjian
     */
    static public function syncLogisticsDeliver()
    {
        set_time_limit(0);
        $list        = DealCjt::SaleDeliveryQueryExecuting();
        $orderNumArr = [];
        foreach ($list['Rows'] as $k => $v) {
            if (!in_array($v['Memo'], $orderNumArr)) {
                try {
                    //出库
                    try {
                        DealYdh::logisticsOutStorage([
                            'orderNum' => $v['Memo'],
                        ]);
                    } catch (AppException $e) {
                        Log::write($e);
                    }
                    //发货
                    $rst = DealYdh::queryAllLogisticsBill(['orderNum' => $v['Memo']]);
                    if ($rst['outStorageBills']) {
                        DealYdh::logisticsDeliver([
                            'billNum' => $rst['outStorageBills'][0]['billNum'],
                        ]);
                    }
                } catch (AppException $e) {
                    Log::write($e);
                    continue;
                }
            } else {
                array_push($orderNumArr, $v['Memo']);

                continue;
            }
        }
    }

    /**
     * syncLogisticsDeliver2 @desc 同步发货单
     *
     * @author wangjian
     */
    static public function syncLogisticsDeliver2()
    {
        set_time_limit(0);
        $list        = DealCjt::SaleDeliveryQueryExecuting();
        $orderNumArr = [];
        foreach ($list['Rows'] as $k => $v) {
            if (!in_array($v['Memo'], $orderNumArr)) {
                try {
                    //if ($v['isSaleOut'] == '已出库' && $v['isCancel'] == '已结清') {
                    $order = DealYdh::orderDetail(['orderNum' => $v['Memo']]);
                    if ($order) {
                        switch ($order['status']) {
                            //财务已审核
                            case YdhOrderApi::STATUS_FINANCIAL_AUDIT2:
                                //出库
                                $rs = DealYdh::logisticsOutStorage([
                                    'orderNum' => $v['Memo'],
                                ]);
                                if ($rs) {
                                    //发货
                                    $rst = DealYdh::queryAllLogisticsBill(['orderNum' => $v['Memo']]);
                                    if ($rst) {
                                        DealYdh::logisticsDeliver([
                                            'billNum' => $rst['outStorageBills'][0]['billNum'],
                                        ]);
                                    }
                                }
                                break;
                            //出库已审核
                            case YdhOrderApi::STATUS_WAEREHOUSE_AUDIT:
                                //发货
                                $rst = DealYdh::queryAllLogisticsBill(['orderNum' => $v['Memo']]);
                                if ($rst) {
                                    DealYdh::logisticsDeliver([
                                        'billNum' => $rst['outStorageBills'][0]['billNum'],
                                    ]);
                                }
                                break;
                            default:
                                break;
                        }
                    }
                    //}
                } catch (AppException $e) {
                    Log::write($e);
                    continue;
                }
            } else {
                array_push($orderNumArr, $v['Memo']);

                continue;
            }
        }
    }
}
