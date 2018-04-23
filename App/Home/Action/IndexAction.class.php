<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 16:58
 */

namespace Clhapp\Home\Action;

class IndexAction extends MainAction
{
    /**
     * index @desc 官网首页入口
     *
     * @author wangjian
     */
    public function index()
    {
        $this->setWebTitle('Home');
        $this->assign('config', $this->getConfig());
        $this->display();
    }

    public function test()
    {
        $this->display();
    }

    /**
     * getConfig @desc 获取配置
     *
     * @author wangjian
     */
    protected function getConfig()
    {
        return addslashes(json_encode([
            'VERSION' => C('VERSION'),
        ], JSON_UNESCAPED_UNICODE));
    }
}