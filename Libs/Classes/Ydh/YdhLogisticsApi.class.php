<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 0:16
 */

namespace Clhapp\Ydh;

use Clhapp\AppException;
use Clhapp\Log;

class YdhLogisticsApi extends YdhApi
{
    const URL_LOGISTICS_DELIVER = '/logistics/deliver.json';
    const URL_LOGISTICS_OUTSTORAGE = '/logistics/outStorage.json';
    const URL_ALL_LOGISTICS_BILL = '/logistics/query_all_logisticsBill.json';

    protected $params = [];

    /**
     * YdhCustomerApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * logisticsDeliver @desc 分批发货接口
     *
     * @author wangjian
     */
    public function logisticsDeliver()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_LOGISTICS_DELIVER, $this->params);
        Log::write($url, 'logisticsDeliver');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            Log::write($res, 'logisticsDeliverError');
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * logisticsOutStorage @desc 分批出库接口
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function logisticsOutStorage()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_LOGISTICS_OUTSTORAGE, $this->params);
        Log::write($url, 'logisticsOutStorage');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            Log::write($res, 'logisticsOutStorageError');
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * queryAllLogisticsBill @desc 出库发货单详情
     *
     * @author wangjian
     */
    public function queryAllLogisticsBill()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_ALL_LOGISTICS_BILL, $this->params);
        Log::write($url, 'queryAllLogisticsBill');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            Log::write($res, 'queryAllLogisticsBillError');
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