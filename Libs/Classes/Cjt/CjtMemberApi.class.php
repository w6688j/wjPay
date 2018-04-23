<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 0:56
 */

namespace Clhapp\Cjt;

class CjtMemberApi extends CjtApi
{
    const URL_MEMBER_CREATEBATCH = '/member/CreateMember';

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
     * createMember @desc 创建会员
     *
     * @author wangjian
     * @return mixed
     */
    public function createMember()
    {
        return json_decode(
            $this->tokenPost(
                $this->url_host . self::URL_MEMBER_CREATEBATCH
                , $this->access_token
                , ['_args' => json_encode(['dto' => $this->params, 'param' => ['IsMemberState' => true]])])
            , true
        );
    }
}