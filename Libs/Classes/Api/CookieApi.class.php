<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/17
 * Time: 0:02
 */

namespace Clhapp\Api;

use Clhapp\Log;

abstract class CookieApi
{
    const FIELD_VALIDATE = 'validate';
    const FIELD_TYPE = 'type';
    const FIELD_REQUIRE = 'require';
    const FIELD_METHOD = 'method';
    const FIELD_ERRMSG = 'msg';
    //post传递
    const METHOD_POST = 'post';
    //get 传递
    const METHOD_GET = 'get';
    //任意传递  $_REQUEST
    const METHOD_ANY = 'any';

    //整形
    const TYPE_INT = 1;
    //布尔值
    const TYPE_BOOL = 2;
    //字符串
    const TYPE_STRING = 3;
    //浮点型
    const TYPE_FLOAT = 4;
    //时间类型
    const TYPE_DATE = 5;
    //任意
    const TYPE_ANY = 6;

    protected $definition = [];
    private $_data = [];
    protected $_return = [
        'code' => 0,
        'msg'  => 'ok',
    ];
    protected $_starttime = '';

    abstract public function run();

    /**
     * CookieApi constructor.
     *
     * @throws ApiException
     */
    final public function __construct()
    {
        if (APP_DEBUG) {
            Log::write(__URL__, 'ajax url');
        }
        $this->_starttime = microtime(true);
        $this->setExceptionHandle();
        $this->formatParams();

        if (method_exists($this, '_init')) {
            $this->_init();
        }
    }

    /**
     * formatParams @desc 解析参数
     *
     * @author wangjian
     * @throws ApiException
     */
    private function formatParams()
    {
        foreach ($this->definition as $field => $rule) {
            $value = $this->getValue($field, $rule[self::FIELD_METHOD]);
            //验证是否需要该参数
            if ($rule[self::FIELD_REQUIRE] !== false && is_null($value)) {
                //参数为必须，却接受到了null值
                throw new ApiException($rule[self::FIELD_ERRMSG] ?: "$field 必须传递~", 500);

            }
            if ($rule[self::FIELD_REQUIRE] === false && is_null($value)) {
                continue;
            }

            //验证某个参数是否合法
            if (isset($rule[self::FIELD_VALIDATE])) {
                if (!$this->validate($value, $rule[self::FIELD_VALIDATE])) {
                    throw new ApiException($rule[self::FIELD_ERRMSG] ?: 'PARAM ' . $field . ' IS INVALID;', 501);
                }
            }
            $this->$field = $this->formatValue($rule[self::FIELD_TYPE], $value);
        }
    }

    /**
     * getValue @desc 获取值
     *
     * @author wangjian
     *
     * @param string $field  字段名称
     * @param string $method 来源
     *
     * @return null
     */
    private function getValue($field, $method = self::METHOD_ANY)
    {
        switch ($method) {
            case self::METHOD_GET:
                return isset($_GET[$field]) ? $_GET[$field] : null;
            case self::METHOD_POST:
                return isset($_POST[$field]) ? $_POST[$field] : null;
            default:
                return isset($_REQUEST[$field]) ? $_REQUEST[$field] : null;
        }
    }

    /**
     * formatValue @desc 解析值
     *
     * @author wangjian
     *
     * @param string $type  类型
     * @param mixed  $value 值
     *
     * @return bool|float|int|string
     */
    private function formatValue($type, $value)
    {
        switch ($type) {
            case self::TYPE_INT:
                return (int)$value;
            case self::TYPE_STRING:
                return (string)$value;
            case self::TYPE_BOOL:
                return $value == true;
            case self::TYPE_FLOAT:
                return (float)$value;
            default:
        }

        return $value;
    }

    /**
     * validate @desc 返回验证规则 成功还是失败
     *
     * @author wangjian
     *
     * @param string $value        值
     * @param string $validateRule 验证规则
     *
     * @return bool
     */
    private function validate($value, $validateRule)
    {
        if ($validateRule && method_exists(Validate::class, $validateRule)) {
            return Validate::$validateRule($value) == true;
        }

        return true;
    }

    /**
     * error @desc 处理错误
     *
     * @author wangjian
     *
     * @param      $code
     * @param      $msg
     * @param bool $background
     */
    public function error($code, $msg, $background = false)
    {
        $this->_return = [
            'code' => $code,
            'msg'  => $msg,
        ];
        $this->calcost();

        api_return($this->_return, true);
        $this->log();
        if (!$background) {
            exit();
        }
    }

    /**
     * success @desc 成功返回
     *
     * @author wangjian
     *
     * @param array $data       返回数据
     * @param bool  $background 是否返回执行
     */
    public function success($data = [], $background = false)
    {
        if (!is_array($data)) {
            $data = [];
        }
        $this->_return = array_merge($this->_return, $data);
        $this->calcost();
        api_return($this->_return, true);
        $this->log();
        if (!$background) {
            exit();
        }
    }

    /**
     * calcost @desc 计算该接口时间消耗和内存消耗
     *
     * @author wangjian
     */
    protected function calcost()
    {
        $this->_data['cost_time'] = sprintf('%.6f', microtime(true) - $this->_starttime);
        $this->_data['cost_mem']  = round(
            memory_get_usage(true) / 1048576, 6
        );
        $cost_str
                                  = 'time cost:' . $this->_data['cost_time'] . 's memery cost:'
            . $this->_data['cost_mem']
            . " megabytes";
        Log::record($cost_str, 'api');
    }

    /**
     * setExceptionHandle @desc 设置拦截器
     *
     * @author wangjian
     */
    protected function setExceptionHandle()
    {
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * exceptionHandler @desc 异常拦截
     *
     * @author wangjian
     *
     * @param \Exception $e
     */
    public function exceptionHandler(\Exception $e)
    {
        $this->error($e->getCode() ?: 8888, $e->getMessage());
    }

    /**
     * log @desc
     *
     * @author wangjian
     */
    private function log()
    {
        //会话id 客户端ip  请求时间 请求地址 返回状态码 返回消息 用户浏览器  用户id和昵称 请求时间 占用内存
        //%h [%d %t] "%r" %s %b [%u] %L
        //time-format %H:%M:%S
        //date-format %Y-%m-%d

        //goaccess -f  api.cutlog   --date-format=%Y-%m-%d --time-format=%H:%M:%S --log-format='%^ %h [%d %t] %r %s %^ [%u] %^ %T %^'
        //goaccess -f  api.cutlog   --date-format=%Y-%m-%d --time-format=%H:%M:%S --log-format='%h [%d %t] "%r" %s %b [%u] %L'

        $log = sprintf("%s %s [%s] %s %s %s [%s] %s %s %s\n"
            , SESSION_ID
            , $_SERVER['REMOTE_ADDR']
            , date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])
            , __URL__
            , $this->_return['code']
            , $this->_return['msg']
            , $_SERVER['HTTP_USER_AGENT']
            , 'LOGIN_UID' . " - " . 'LOGIN_NICKNAME'
            , $this->_data['cost_time']
            , $this->_data['cost_mem']
        );
        error_log($log, 3, C('APILOG_PATH'));
    }
}