<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/17
 * Time: 0:01
 */

namespace Clhapp\Api;

abstract class HomeApi extends CookieApi
{
    /**
     * success @desc
     *
     * @author wangjian
     *
     * @param array $data       返回数组
     * @param bool  $background 是否继续执行
     */
    public function success($data = [], $background = false)
    {
        parent::success(['data' => $data], $background);
    }
}