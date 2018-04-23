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

class YdhCustomerApi extends YdhApi
{
    const URL_CUSTOMER_TYPE_LIST = '/customer/customer_type_list.json';
    const URL_CUSTOMER_LIST = '/customer/customer_list.json';
    const URL_CUSTOMER_CREATE = '/customer/customer_create.json';

    protected $params = [];

    /**
     * YdhCustomerApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * customerTypeList @desc 客户类型列表
     *
     * @author wangjian
     */
    public function customerTypeList()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_CUSTOMER_TYPE_LIST, $this->params);
        Log::write($url, 'customerTypeList');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * customerList @desc 客户列表
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function customerList()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_CUSTOMER_LIST, $this->params);
        Log::write($url, 'customerList');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * customerCreate @desc 新增客户
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function customerCreate()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_CUSTOMER_CREATE, $this->params);
        Log::write($url, 'customerCreate');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            Log::write($res, 'customerCreateError');
            //throw new AppException($res['message'], $res['code']);
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