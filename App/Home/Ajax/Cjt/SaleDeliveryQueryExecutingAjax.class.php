<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/3
 * Time: 23:29
 */

namespace Clhapp\Home\Ajax\Cjt;

use Clhapp\Api\CookieApi;
use Clhapp\Cjt\DealCjt;

class SaleDeliveryQueryExecutingAjax extends CookieApi
{
    public function run()
    {
        $this->success(DealCjt::SaleDeliveryQueryExecuting());
    }
}