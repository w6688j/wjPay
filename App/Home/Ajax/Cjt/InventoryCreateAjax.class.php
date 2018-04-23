<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 14:35
 */

namespace Clhapp\Home\Ajax\Cjt;

use Clhapp\Api\CookieApi;
use Clhapp\Cjt\DealCjt;

class InventoryCreateAjax extends CookieApi
{
    /**
     * run @desc 畅捷通存货创建接口
     *
     * @author wangjian
     */
    public function run()
    {
        $rst = DealCjt::InventoryCreate([
            'Code'           => '99005',
            'Name'           => '中南海1mg',
            'Shorthand'      => 'ZNH1MG',
            'Specification'  => '1g',
            'DefaultBarCode' => '9900501',
            'InventoryClass' => ['Code' => '99', 'Name' => '管子'],
            'ProductInfo'    => ['Code' => '01', 'Name' => '中南海'],
            'Unit'           => ['Code' => '21', 'Name' => 'KG'],
            'IsPurchase'     => 'True',
            'IsSale'         => 'False',
            'IsMadeSelf'     => 'False',
            'IsMaterial'     => 'False',
            'IsSuite'        => 'False',
            'IsLaborCost'    => 'False',
        ]);

        print_r($rst);
    }
}