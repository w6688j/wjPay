<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 0:56
 */

namespace Clhapp\Cjt;

class CjtSaleDispatchApi extends CjtApi
{
    const URL_SALEDISPATH_QUERY = '/saleDispatch/Query';

    protected $params = [];

    /**
     * CjtMemberApi constructor.
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
     * saleDispatchQuery @desc 销售出库单查询
     *
     * @author wangjian
     * @return mixed
     */
    public function saleDispatchQuery()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_SALEDISPATH_QUERY
                , $this->access_token
                , ['_args' => json_encode(['queryParam' => $this->params])])
            , true
        );
    }
}