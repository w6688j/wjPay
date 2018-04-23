<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/2
 * Time: 21:39
 */

namespace Clhapp\Ydh;

use Clhapp\AppException;
use Clhapp\Log;

class YdhApi
{
    const URL_TOKEN = '/oauth2/token';
    const LIST_SIZE = 100;
    const LIST_TYPE_LIMIT = 'limit';
    const LIST_TYPE_PAGE = 'page';

    protected $config;
    protected $client_id;
    protected $client_secret;
    protected $user_name;
    protected $password;
    protected $url_host;
    protected $access_token;

    /**
     * YdhApi constructor.
     *
     * @throws AppException
     */
    public function __construct()
    {
        $this->config = C('YDH_CONF');
        if (!$this->config['client_id']) {
            throw new AppException('client_id不合法~', 'CLIENT_ID_INVAILD');
        }
        if (!$this->config['client_secret']) {
            throw new AppException('client_secret不合法~', 'CLIENT_SECRET_INVAILD');
        }
        if (!$this->config['user_name']) {
            throw new AppException('user_name不合法~', 'USER_NAME_INVAILD');
        }
        if (!$this->config['password']) {
            throw new AppException('password不合法~', 'PASSWORD_INVAILD');
        }
        if (!$this->config['url_host']) {
            throw new AppException('url_host不合法~', 'URL_HOST_INVAILD');
        }

        $this->client_id     = $this->config['client_id'];
        $this->client_secret = $this->config['client_secret'];
        $this->user_name     = $this->config['user_name'];
        $this->password      = $this->config['password'];
        $this->url_host      = $this->config['url_host'];

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
     * creatAccessToken @desc Client Credentials 授权获取AccessToken 超时时间一个月
     *
     * @author wangjian
     */
    public function creatAccessToken()
    {
        $path = VPATH . "/wwwroot/Home/cache/access_token.php";
        $data = json_decode($this->get_php_file($path));
        if ($data->expire_time < time()) {
            Log::write($this->getAccessTokenUrl());
            $res = json_decode(curl($this->getAccessTokenUrl()), true);
            if ($res['data']['access_token']) {
                Log::write('get new access token');
                $data->expire_time   = time() + 600;
                $data->access_token  = $res['data']['access_token'];
                $data->scope         = $res['data']['scope'];
                $data->refresh_token = $res['data']['refresh_token'];
                $data->create_time   = $res['data']['create_time'];
                $data->nodeCode      = $res['data']['nodeCode'];
                $this->set_php_file($path, json_encode($data));

                $this->access_token = $data->access_token;
            }
        } else {
            $this->access_token = $data->access_token;
        }

        Log::write($this->access_token, 'access_token');
    }

    /**
     * getAccessTokenUrl @desc 获取Access Token Url
     *
     * @author wangjian
     * @return string
     */
    protected function getAccessTokenUrl()
    {
        return $this->parseUrlParams(self::URL_TOKEN, [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'userName'      => $this->user_name,
            'password'      => $this->password,
        ]);
    }

    /**
     * parseUrlParams @desc 设置url参数
     *
     * @author wangjian
     *
     * @param string $url    具体地址
     * @param array  $params 参数数组
     *
     * @return string
     * @throws AppException
     */
    protected function parseUrlParams($url, $params)
    {
        if (!$params) {
            throw new AppException('请求参数不能为空~', 'PARAMS_INVALID');
        }
        $buff = '';
        $i    = 1;
        foreach ($params as $k => $v) {
            if ($v !== "" && !is_array($v)) {
                if ($i == count($params)) {
                    $buff .= $k . "=" . $v;
                } else {
                    $buff .= $k . "=" . $v . "&";
                }
            }

            $i++;
        }

        return $this->url_host . $url . '?' . $buff;
    }

    /**
     * get_php_file @desc 读取本地文件
     *
     * @author wangjian
     *
     * @param string $filename 文件名称
     *
     * @return string
     */
    protected function get_php_file($filename)
    {
        if (file_exists($filename)) {
            return trim(substr(file_get_contents($filename), 15));
        } else {
            return '{"expire_time":0}';
        }
    }

    /**
     * set_php_file @desc 写入本地文件
     *
     * @author wangjian
     *
     * @param string $filename 文件名
     * @param string $content  内容
     */
    protected function set_php_file($filename, $content)
    {
        file_put_contents($filename, "<?php exit();?>" . $content);
    }
}