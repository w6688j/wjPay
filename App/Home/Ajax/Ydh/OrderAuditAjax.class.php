<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/5
 * Time: 22:36
 */

namespace Clhapp\Home\Ajax\Ydh;

use Clhapp\Api\CookieApi;
use Clhapp\Ydh\DealYdh;

class OrderAuditAjax extends CookieApi
{
    public function run()
    {
        $this->success(DealYdh::orderAudit([
            'orderNum'     => 'DH-O-20170505-044128',
            'version'      => '3',
            'returnDetail' => true,
        ]));
    }
}