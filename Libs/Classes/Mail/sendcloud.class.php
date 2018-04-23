<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2016/8/28
 * Time: 12:25
 */

namespace Clhapp\Mail;

use Clhapp\AppException;

class sendcloud
{
    const URL_SEND = 'http://api.notice.sendcloud.net/mailapi/send';

    private $config;
    private $accessKey;
    private $secretKey;
    private $subject = '';
    private $content = '';
    private $nickNames = '';

    public function __construct($ini = 'SENDCLOUD')
    {
        $this->config = C($ini);
        if (empty($this->config['accessKey'])) {
            throw new AppException("发送邮件配置不存在~", 45001);
        }
        if (empty($this->config['secretKey'])) {
            throw new AppException("发送邮件配置不存在~", 45002);
        }
        $this->accessKey = $this->config['accessKey'];
        $this->secretKey = $this->config['secretKey'];
    }

    /**
     * Compute Signature.
     *
     * @param  array $param
     *
     * @access public
     * @return string
     */
    private function getSignature($param)
    {
        ksort($param);
        $str = '';
        foreach ($param as $key => $value) $str .= $key . '=' . $value . '&';

        return md5($this->secretKey . '&' . $str . $this->secretKey);
    }

    /**
     * Query Sendcloud
     *
     * @param  string $url   地址
     * @param  array  $param 参数
     *
     * @access public
     * @return object
     */
    private function query($url, $param)
    {
        if (!isset($param['signature'])) $param['signature'] = $this->getSignature($param);

        $str = http_build_query($param);

        $result = file_get_contents($url . '?' . $str);

        return json_decode($result);
    }

    /**
     * @desc   setSubject 设置主题
     * @author wangjian
     *
     * @param string $subject 主题内容
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @desc   setContent 设置邮件内容
     * @author wangjian
     *
     * @param string $content 设置邮件内容
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function send()
    {
        $param['accessKey'] = $this->accessKey;
        $param['nickNames'] = preg_replace('/[^a-zA-z0-9@\._;]/', '_', $this->nickNames);
        $param['subject']   = $this->subject;
        $param['content']   = $this->content;

        $result = $this->query(self::URL_SEND, $param);
        if ($result->result == false) {
            throw new AppException($result->message . "(code:{$result->statusCode})", $result->statusCode);
        }
    }

    public function send2()
    {
        $API_USER = 'w6688j';
        $API_KEY  = 'dxtebDBa5INgrEPx';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'http://sendcloud.sohu.com/webapi/mail.send.json');

        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key'  => $API_KEY,
            'from'     => 'dev@mail.dev.izhuyan.com', # 发信人，用正确邮件地址替代
            'fromname' => 'Home',
            'to'       => $this->nickNames, # 收件人地址，用正确邮件地址替代，多个地址用';'分隔
            'subject'  => $this->subject,
            'html'     => $this->content,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            echo curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

    /**
     * @desc   setAddress 设置地址
     * @author wangjian
     *
     * @param array | string $address
     */
    public function setAddress($address)
    {
        if (is_array($address)) {
            $this->nickNames = implode(';', $address);
        } else {
            $this->nickNames = $address;
        }

    }
}