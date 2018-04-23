<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 0:56
 */

namespace Clhapp\Cjt;

class CjtPartnerApi extends CjtApi
{
    const URL_PARTNER_CREATE = '/partner/Create';
    const URL_PARTNER_QUERY = '/partner/Query';

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
     * createPartner @desc 往来单位创建
     *
     * @author wangjian
     * @return mixed
     */
    public function createPartner()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_PARTNER_CREATE
                , $this->access_token
                , ['_args' => json_encode(['dto' => $this->params])])
            , true
        );
    }

    /**
     * partnerQuery @desc 往来单位查询
     *
     * @author wangjian
     * @return mixed
     */
    public function partnerQuery()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_PARTNER_QUERY
                , $this->access_token
                , ['_args' => json_encode($this->params)])
            , true
        );
    }
}