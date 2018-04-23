<?php
namespace Clhapp;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 15:32
 */
class Action
{
    /**
     * @var View
     */
    private $view;
    protected $returnData = [];

    /**
     * Action constructor. 构造函数 不允许继承
     */
    final public function __construct()
    {
        self::__initialize();
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }

    /**
     * __initialize @desc 子类初始化函数 可以重写
     *
     * @author wangjian
     */
    final private function __initialize()
    {
        $this->view = new View();
        if (IS_AJAX) {
            //AJAX 请求 初始化返回数据
            $this->returnData = [
                'code'    => 0,
                'message' => 'success',
                'action'  => '',
                'url'     => '',
            ];
        }
    }

    /**
     * assign @desc 模板赋值
     *
     * @author wangjian
     *
     * @param string $name  变量名
     * @param mixed  $value 变量值
     */
    protected function assign($name, $value = '')
    {
        $this->view->assign($name, $value);
    }

    /**
     * display @desc 渲染模板
     *
     * @author wangjian
     *
     * @param string $tpl   模板名
     * @param bool   $print 是否打印
     *
     * @return mixed|string
     * @throws AppException
     */
    protected function display($tpl = '', $print = true)
    {
        if (empty($tpl)) {
            $tpl = APP_PATH . '/Tpl/' . ucfirst(MODULE_NAME) . '/' . ACTION_NAME . C('TEMPLATE_EXT');
        } elseif (strpos($tpl, '/') === false) {
            strpos($tpl, '.') === false && $tpl .= C('TEMPLATE_EXT');
            $tpl = APP_PATH . '/Tpl/' . ucfirst(MODULE_NAME) . '/' . $tpl;
        } else {
            strpos($tpl, '.') === false && $tpl .= C('TEMPLATE_EXT');
            list($m, $a) = explode('/', $tpl, 2);
            $tpl = APP_PATH . '/Tpl/' . ucfirst($m) . '/' . $a;
        }
        if (!is_file($tpl)) {
            throw new AppException($tpl . ' 模板文件不存在');
        }

        return $this->view->display($tpl, $print);
    }

    /**
     * ajaxReturn @desc ajax返回
     *
     * @author wangjian
     *
     * @param bool $background
     */
    protected function ajaxReturn($background = false)
    {
        api_return($this->returnData, $background);
    }

    /**
     * doSomeThing @desc
     *
     * @author wangjian
     *
     * @param string $do_name 操作名
     * @param string $do_url  操作url
     */
    protected function doSomeThing($do_name = '', $do_url = '')
    {
        if (empty($do_name)) {
            $do_name = '返回上一页';
        }

        if (empty($do_url)) {
            $do_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'javascript:;';
        }

        $this->assign('do_url', $do_url);
        $this->assign('do_name', $do_name);
    }

    /**
     * error @desc 返回错误
     *
     * @author wangjian
     *
     * @param string $message 错误信息
     * @param string $jumpUrl 跳转url
     * @param bool   $ajax    是否ajax返回
     */
    protected function error($message = '', $jumpUrl = '', $ajax = false)
    {
        Log::record($message, 'action-error');
        $this->dispatchJump($message, 0, $jumpUrl, $ajax);
    }

    /**
     * success @desc 成功返回
     *
     * @author wangjian
     *
     * @param string $message 提示信息
     * @param string $jumpUrl 跳转url
     * @param bool   $ajax    是否ajax返回
     */
    protected function success($message = '', $jumpUrl = '', $ajax = false)
    {
        $this->dispatchJump($message, 1, $jumpUrl, $ajax);
    }

    /**
     * dispatchJump @desc dispatchJump
     *
     * @author wangjian
     *
     * @param string $message 提示信息
     * @param int    $status  状态
     * @param string $jumpUrl 跳转地址
     * @param bool   $ajax    是否ajax返回
     */
    private function dispatchJump($message, $status = 1, $jumpUrl = '', $ajax = false)
    {
        if ($ajax || IS_AJAX) {
            $this->returnData['code']    = $status == 1 ? 0 : 500;
            $this->returnData['message'] = $message;
            $this->returnData['msg']     = $message;
            if ($jumpUrl) {
                $this->returnData['url'] = $jumpUrl;
            }
            $this->ajaxReturn();

            return;
        }
        $jumpUrl = $jumpUrl ? $jumpUrl : ($_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : C('SITEURL'));
        if ($status === 1) {
            $waitSecond = 1;
        } else {
            $waitSecond = 3;
        }
        $this->assign('success', $status === 1);
        $this->assign('message', $message);
        $this->assign('jumpUrl', $jumpUrl);
        $this->assign('waitSecond', $waitSecond);
        $this->display(C('DISPATCH_JUMP_TPL'));
        exit();
    }
}