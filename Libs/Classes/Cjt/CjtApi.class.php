<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 23:50
 */

namespace Clhapp\Cjt;

use Clhapp\AppException;
use Clhapp\Log;

class CjtApi
{
    const LIST_SIZE = 100;

    const URL_TOKEN = '/Authorization';
    const URL_RELOGIN = '/Authorization/ReLogin';

    protected $config;
    protected $app_key;
    protected $app_secret;
    protected $user_name;
    protected $password;
    protected $account_number;
    protected $url_host;
    protected $access_token;

    /**
     * CjtApi constructor.
     *
     * @throws AppException
     */
    public function __construct()
    {
        $this->config = C('CJT_CONF');
        if (!$this->config['app_key']) {
            throw new AppException('app_key不合法~', 'APP_KEY_INVAILD');
        }
        if (!$this->config['app_secret']) {
            throw new AppException('app_secret不合法~', 'APP_SECRET_INVAILD');
        }
        if (!$this->config['user_name']) {
            throw new AppException('user_name不合法~', 'USER_NAME_INVAILD');
        }
        if (!$this->config['account_number']) {
            throw new AppException('account_number不合法~', 'ACCOUNT_NUMBER_INVAILD');
        }
        if (!$this->config['url_host']) {
            throw new AppException('url_host不合法~', 'URL_HOST_INVAILD');
        }

        $this->app_key        = $this->config['app_key'];
        $this->app_secret     = $this->config['app_secret'];
        $this->user_name      = $this->config['user_name'];
        $this->password       = $this->config['password'];
        $this->account_number = $this->config['account_number'];
        $this->url_host       = $this->config['url_host'];

        $this->init();
    }

    /**
     * init @desc 初始化
     *
     * @author wangjian
     */
    protected function init()
    {
        $this->creatAccessToken();
    }

    /**
     * creatAccessToken @desc 创建Access Token
     *
     * @author wangjian
     */
    protected function creatAccessToken()
    {
        $res = json_decode($this->post($this->url_host . self::URL_TOKEN, ["_args" => json_encode([
            'UserName'      => $this->user_name,
            'Password'      => $this->parsePassword(),
            'AccountNumber' => $this->account_number,
            'LoginDate'     => date('Y-m-d H:i:s', time()),
        ])]));

        if (!empty($res->result)) {
            $this->access_token = $res->access_token;
        } else {
            if ($res->code = 'EXSM0004') {
                //用户已登录，需要调用重新登录接口
                $token              = $res->data;
                $res                = json_decode($this->tokenPost($this->url_host . self::URL_RELOGIN, $token));
                $this->access_token = $res->access_token;
            } else {
                throw new AppException($res, 'SOMETHING_WRONG');
            }

        }
    }

    /**
     * parsePassword @desc 解析密码
     *
     * @author wangjian
     * @return string
     */
    protected function parsePassword()
    {
        return base64_encode(md5($this->password, true));
    }

    /**
     * AuthSign @desc 设置签名
     *
     * @author wangjian
     *
     * @param string $uri   地址
     * @param string $token token
     *
     * @return string
     */
    private function AuthSign($uri, $token = '')
    {
        $param = [
            'uri'          => $uri,
            'access_token' => $token,
            'date'         => gmdate('l, d M Y H:i:s') . ' GMT',
        ];

        $authinfo = base64_encode(hash_hmac("sha1", stripslashes(json_encode($param)), $this->app_secret, true));
        $auth     = [
            'appKey'    => $this->app_key,
            'authInfo'  => 'hmac-sha1 ' . $authinfo,
            'paramInfo' => $param,
        ];

        return base64_encode(stripslashes(json_encode($auth)));
    }

    /**
     * tokenPost @desc 携带token请求
     *
     * @author wangjian
     *
     * @param string $uri   地址
     * @param string $token token
     * @param array  $args  参数
     *
     * @return mixed
     */
    public function tokenPost($uri, $token, $args = [])
    {
        return $this->curlPost($uri, [
            "Content-type:application/x-www-form-urlencoded;charset=utf-8",
            "Authorization:" . $this->AuthSign($uri, $token),
        ], $args);
    }

    /**
     * post @desc 不携带token请求
     *
     * @author wangjian
     *
     * @param string $uri  地址
     * @param array  $args 参数
     *
     * @return mixed
     */
    public function post($uri, $args = [])
    {
        return $this->curlPost($uri, [
            "Content-type:application/x-www-form-urlencoded;charset=utf-8",
            "Authorization:" . $this->AuthSign($uri),
        ], $args);
    }

    /**
     * curlPost @desc
     *
     * @author wangjian
     *
     * @param string $uri    地址
     * @param array  $header 头信息
     * @param array  $args   参数
     *
     * @return mixed
     */
    private function curlPost($uri, $header = [], $args = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));

        Log::write($uri, '$uri');
        Log::write($args, '$args');

        $response = curl_exec($ch);
        Log::write($response, '$response');
        curl_close($ch);

        return $response;
    }

    /**
     * getAccessToken @desc 获取Access Token
     *
     * @author wangjian
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }
}