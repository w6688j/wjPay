<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/21
 * Time: 0:58
 */

namespace Clhapp\Cjt;

class CjtInventoryApi extends CjtApi
{
    const URL_INVENTORY_QUERY = '/inventory/Query';
    const URL_INVENTORY_CLASS_QUERY = '/inventoryClass/Query';
    const URL_INVENTORY_CREATE = '/inventory/Create';
    const URL_ORDER_CERAYE = '/purchaseOrder/Create';
    const URL_CURRENT_STOCK_QUERY = '/currentStock/Query';

    protected $params = [];

    /**
     * CjtInventoryApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * query @desc 存货查询
     *
     * @author wangjian
     */
    public function inventoryQuery()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_INVENTORY_QUERY
                , $this->access_token
                , ['_args' => json_encode($this->params)])
            , true
        );
    }

    /**
     * inventoryClassQuery @desc 存货分类查询
     *
     * @author wangjian
     * @return mixed
     */
    public function inventoryClassQuery()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_INVENTORY_CLASS_QUERY
                , $this->access_token
                , ['_args' => json_encode($this->params)])
            , true
        );
    }

    /**
     * inventoryCreate @desc 存货创建
     *
     * @author wangjian
     * @return mixed
     */
    public function inventoryCreate()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_INVENTORY_CREATE
                , $this->access_token
                , ['_args' => json_encode(['dto' => $this->params])])
            , true
        );
    }

    /**
     * currentStockQuery @desc 现存量查询
     *
     * @author wangjian
     * @return mixed
     */
    public function currentStockQuery()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_CURRENT_STOCK_QUERY
                , $this->access_token
                , ['_args' => json_encode(['param' => $this->params])])
            , true
        );
    }

    /**
     * orderCreate @desc 创建采购订单
     *
     * @author wangjian
     */
    public function orderCreate()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_ORDER_CERAYE
                , $this->access_token
                , ['_args' => json_encode(['purchaseOrderDTO' => $this->params])])
            , true
        );
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
}