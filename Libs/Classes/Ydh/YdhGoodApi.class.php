<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 23:14
 */

namespace Clhapp\Ydh;

use Clhapp\AppException;
use Clhapp\Log;

class YdhGoodApi extends YdhApi
{
    const SIZE = 10;//每次拉取条数

    const URL_GOOD_CREATE = '/goods/goods_create.json';
    const URL_GOOD_LIST = '/goods/goods_list.json';
    const URL_GOOD_TYPE_LIST = '/goods/goods_type_list.json';
    const URL_GOOD_UNIT_LIST = '/goods/goods_unit_list.json';
    const URL_GOOD_INVENTORY = '/goods/goods_inventory.json';

    protected $params = [];

    /**
     * YdhGoodApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * goodCreate @desc 新增商品
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function goodCreate()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_GOOD_CREATE, $this->params);
        Log::write($url, 'goodCreate');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            Log::write($res, 'goodCreateError');
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * goodList @desc 商品列表
     *
     * @author wangjian
     */
    public function goodList()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_GOOD_LIST, $this->params);
        Log::write($url, 'goodList');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * goodTypeList @desc 商品分类列表
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function goodTypeList()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_GOOD_TYPE_LIST, $this->params);
        Log::write($url, 'goodTypList');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * goodUnitList @desc 商品单位列表
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function goodUnitList()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_GOOD_UNIT_LIST, $this->params);
        Log::write($url, 'goodUnitList');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
            throw new AppException($res['message'], $res['code']);
        }

        return $res['data'];
    }

    /**
     * inventory @desc 商品库存修改
     *
     * @author wangjian
     * @return mixed
     * @throws AppException
     */
    public function inventory()
    {
        $this->setParams('access_token', $this->access_token);
        $url = $this->parseUrlParams(self::URL_GOOD_INVENTORY, $this->params);
        Log::write($url, 'goodinventory');

        $res = json_decode(curl($url), true);
        if ($res['code'] != '200') {
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