<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 18:57
 */

namespace Clhapp\Cjt;

class CjtSaleOrderApi extends CjtApi
{
    const URL_SALEORDER_CERAYE = '/saleOrder/Create';

    protected $params = [];

    /**
     * CjtSaleOrderApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * setParams @desc 设置参数
     *
     * @author wangjian
     *
     * @param string $key 键名
     * @param string $val 键值
     *
     * @return $this
     */
    public function setParams($key, $val)
    {
        $this->params[$key] = $val;

        return $this;
    }

    /**
     * create @desc 创建销售订单
     *
     * @author wangjian
     * @return mixed
     */
    public function create()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_SALEORDER_CERAYE
                , $this->access_token
                , ['_args' => json_encode(['dto' => $this->params])])
            , true
        );
    }
}