<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 17:12
 */

namespace Clhapp\Home\Action;

use Clhapp\Action;
use Clhapp\Log;

class MainAction extends Action
{
    protected $userObj;

    /**
     * _initialize 初始化
     *
     * @author wangjian
     */
    final public function _initialize()
    {
        Log::write(__URL__, '__URL__');
        //$checklogin = true;
        $checklogin = false;
        if (C('NOLOGIN_MODULE')) {
            if (in_array(MODULE_NAME, explode(',', C('NOLOGIN_MODULE')))) {
                //判断模块名称是否在不需要登陆验证的模块列表中 如果不在 则加载OA登陆验证
                $checklogin = false;
            }
        }

        if ($checklogin && C('NOLOGIN_ACTION')) {
            $nologinActionArray = explode(',', C('NOLOGIN_ACTION'));
            if (in_array(MODULE_NAME . '.' . ACTION_NAME, $nologinActionArray)) {
                $checklogin = false;
            }
        }
        //初始化函数
        if (method_exists($this, '_init')) {
            $this->_init();
        }

        if (!IS_AJAX && !IS_CLI) {
            $this->setWebTitle(C('WEB_TITLE'));
            $this->assign('tongji_js', C('BAIDU_TJ'));
        }
    }

    /**
     * setWebTitle @desc 设置页面标题
     *
     * @author wangjian
     *
     * @param string $title 标题名称
     */
    protected function setWebTitle($title)
    {
        $this->assign('web_title', $title);
    }

    /**
     * ajaxReturnHtml @desc 返回html
     *
     * @author wangjian
     *
     * @param string $html 返回html
     * @param int    $code 返回状态码
     */
    protected function ajaxReturnHtml($html, $code = 0)
    {
        $this->returnData = [
            'code'    => $code,
            'message' => '获取成功',
            'data'    => [
                'html' => $html,
            ],
        ];

        $this->ajaxReturn();
    }
}