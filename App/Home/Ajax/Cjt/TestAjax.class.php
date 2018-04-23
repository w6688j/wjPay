<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/4/19
 * Time: 19:29
 */

namespace Clhapp\Home\Ajax\Cjt;

use Clhapp\Api\CookieApi;

class TestAjax extends CookieApi
{
    /**
     * run
     *
     * @author root
     * @time   2018/4/19 19:32
     */
    public function run()
    {
        $this->success(['code' => 1, 'msg' => 'successfully']);
    }
}