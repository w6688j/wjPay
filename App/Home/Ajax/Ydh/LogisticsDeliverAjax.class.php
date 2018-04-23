<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/3
 * Time: 23:43
 */

namespace Clhapp\Home\Ajax\Ydh;

use Clhapp\Api\CookieApi;
use Clhapp\Ydh\DealYdh;

class LogisticsDeliverAjax extends CookieApi
{
    public function run()
    {
        $rst = DealYdh::queryAllLogisticsBill(['orderNum' => 'DH-O-20170505-044128']);

        $this->success(DealYdh::logisticsDeliver([
            'billNum' => $rst['outStorageBills'][0]['billNum'],
        ]));
    }
}