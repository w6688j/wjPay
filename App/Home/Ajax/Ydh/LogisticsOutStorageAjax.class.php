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

class LogisticsOutStorageAjax extends CookieApi
{
    public function run()
    {
        $this->success(DealYdh::logisticsOutStorage([
            'orderNum' => 'DH-O-20170505-044128',
        ]));
    }
}