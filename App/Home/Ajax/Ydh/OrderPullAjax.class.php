<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/18
 * Time: 17:40
 */

namespace Clhapp\Home\Ajax\Ydh;

use Clhapp\Api\CookieApi;
use Clhapp\Cjt\DealCjt;
use Clhapp\Extend\HeartBeat;
use Clhapp\Sync\CjtSync;
use Clhapp\Sync\YdhSync;
use Clhapp\Ydh\DealYdh;
use Clhapp\Ydh\YdhOrderApi;

class OrderPullAjax extends CookieApi
{
    /**
     * run @desc 拉取订单接口
     *
     * @author wangjian
     */
    public function run()
    {
        //HeartBeat::run();
        //$list = DealYdh::customerList(1);
        //CjtSync::syncPartner();exit;
        //$list = DealYdh::orderAllList([], 1);
        //$this->success(DealYdh::orderAllList([], 1));exit;
        //CjtSync::syncSaleOrder();exit;

        //YdhSync::syncGood();exit;
        /*$list = DealYdh::pullOrder();*/
        //::syncInventory();exit;
        //YdhSync::syncCustomer();exit;
        //YdhSync::syncLogisticsDeliver();exit;
        /*$rst = DealYdh::goodCreate([
            'name'            => 'wdwewq',
            'code'            => 'wdwewq',
            'spec'            => '个',
            //'productTypeId'   => $good['InventoryClass']['Code'],
            //'productUnitId'   => $good['Unit']['Code'],
            //'productUnitName' => $good['Unit']['Name'],
            'productTypeId'   => 2968182,
            'productUnitId'   => 2225357,
            'productUnitName' => '个',
            'marketPrice'     =>  1,
            'orderPrice'      =>  1,
            'imgUrl'          => '',
            'barcode'         => '',
        ]);

        $this->success($rst);*/

        $this->success(DealCjt::inventoryQuery());exit;
        //$this->success(DealYdh::goodList());
        /*$this->success(DealCjt::currentStockQuery([
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
        ]));*/
    }
}