<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2016/8/28
 * Time: 12:25
 */

namespace crontab;

class CronEmail
{
    const URL_SEND = 'http://api.notice.sendcloud.net/mailapi/send';

    private $subject = '';
    private $content = '';
    private $nickNames = '';

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

    /**
     * send @desc 发送邮件
     *
     * @author wangjian
     * @return mixed
     */
    public function send()
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
            'fromname' => substr('cron-' . ENV, 0, 25),
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