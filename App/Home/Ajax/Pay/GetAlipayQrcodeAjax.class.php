<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/4/19
 * Time: 23:54
 */

namespace Clhapp\Home\Ajax\Pay;

use Clhapp\Api\CookieApi;

class GetAlipayQrcodeAjax extends CookieApi
{
    protected $type;
    protected $trade_no;
    protected $total_fee;
    protected $s;

    protected $definition = [
        'type'      => [
            self::FIELD_TYPE   => self::TYPE_INT,
            self::FIELD_METHOD => self::METHOD_GET,
        ],
        'trade_no'  => [
            self::FIELD_TYPE   => self::TYPE_STRING,
            self::FIELD_METHOD => self::METHOD_GET,
        ],
        'total_fee' => [
            self::FIELD_TYPE   => self::TYPE_INT,
            self::FIELD_METHOD => self::METHOD_GET,
        ],
        's'         => [
            self::FIELD_TYPE   => self::TYPE_STRING,
            self::FIELD_METHOD => self::METHOD_GET,
        ],
    ];

    protected $return_arr = [
        "status" => false,
        "msg"    => '缺少必要参数',
        "data"   => [],
    ];

    /**
     * run
     *
     * @author root
     * @time   2018/4/20 0:03
     */
    public function run()
    {
        if (!$this->trade_no || !$this->type || !$this->total_fee || !$this->s) {
            $this->success($this->return_arr);
        }

        $this->success(['code' => 1]);
    }
}