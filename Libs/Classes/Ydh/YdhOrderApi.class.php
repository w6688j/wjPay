<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/18
 * Time: 17:35
 */

namespace Clhapp\Ydh;

use Clhapp\AppException;
use Clhapp\Log;

class YdhOrderApi extends YdhApi
{
    const SIZE = 10;//每次拉取条数
    const TRANSPORT_TYPE_UDP = 'udp';//udp模式
    const TRANSPORT_TYPE_TCP = 'tcp';//tcp模式

    const URL_PULL_ORDER = '/order/pull_order.json';
    const URL_ORDER_LIST = '/order/order_all_list.json';
    const URL_ORDER_DETAIL = '/order/order_detail.json';
    const URL_ORDER_AUDIT = '/order/order_audit.json';

    const STATUS_SUBMIT = '0';//订单已提交
    const STATUS_FINANCIAL_AUDIT = '1';//订货单已财务审核
    const STATUS_FINANCIAL_AUDIT2 = '2';//订货单已财务审核
    const STATUS_WAEREHOUSE_AUDIT = '3';//订货单已出库审核
    const STATUS_DELIVERIED = '4';//订货单已发货确认
    const STATUS_RECEIPTED = '5';//订货单已收货确认
    const STATUS_FINISHED = '6';//订货单已完成

    const TYPE_ORDER = 1;//单据类型 订货单
    const TYPE_REFUND = 2;//单据类型 退货单

    protected $params = [];

    /**
     * YdhOrderApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * pullOrder @desc 拉取订单
     *
     * @author wangjian
     *
     * @return array
     * @throws AppException
     */
    public function pullOrder()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_PULL_ORDER, $this->params);
        Log::write($url, 'pullOrder');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * orderAllList @desc 订单列表接口
     *
     * @author wangjian
     * @return array
     * @throws AppException
     */
    public function orderAllList()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_ORDER_LIST, $this->params);
        Log::write($url, 'orderAllList');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * orderDetail @desc 订单详情
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function orderDetail()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_ORDER_DETAIL, $this->params);
        Log::write($url, 'orderDetail');

        $res = json_decode(curl($url), true);
        Log::write($res, 'orderDetail');
        if (!$res) {
            Log::write($res, 'orderDetailError');
            //throw new AppException('请求失败~', 'Require Failed');
        }
        if ($res['code'] != '200') {
            Log::write($res, 'orderDetailError');
            //throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * orderAudit @desc 订单审核
     *
     * @author wangjian
     */
    public function orderAudit()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_ORDER_AUDIT, $this->params);
        Log::write($url, 'orderAudit');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            Log::write($res, 'orderAudit');
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
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