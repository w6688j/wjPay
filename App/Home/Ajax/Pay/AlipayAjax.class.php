<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/4/19
 * Time: 23:42
 */

namespace Clhapp\Home\Ajax\Pay;

use Clhapp\Api\CookieApi;

class AlipayAjax extends CookieApi
{
    /**
     * run
     *
     * @author root
     * @time   2018/4/19 23:43
     */
    public function run()
    {
        $this->success(['code' => 1, 'msg' => 'Alipay']);
    }
}