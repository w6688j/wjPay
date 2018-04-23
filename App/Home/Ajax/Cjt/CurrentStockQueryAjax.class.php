<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/1
 * Time: 19:47
 */

namespace Clhapp\Home\Ajax\Cjt;

use Clhapp\Api\CookieApi;
use Clhapp\Cjt\DealCjt;

class CurrentStockQueryAjax extends CookieApi
{
    /**
     * run @desc 查询存货现存量
     *
     * @author wangjian
     */
    public function run()
    {
        $this->success(DealCjt::currentStockQuery([
            'Warehouse'          => [['Code' => '001']],
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
        ]));
    }
}