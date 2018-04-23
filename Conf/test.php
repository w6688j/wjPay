<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/4
 * Time: 0:15
 */

return [
    'VERSION' => '1.0.1',//版本号 程序版本

    'APP_HOST'         => 'oa.anxin.chenmm.cn',//该网址默认域名
    'HOST_CONFIG'      => [
        'default'                    => 'default',
        //测试环境
        'oa.test.axhome.com.cn'      => 'test',
        'admin.test.axhome.com.cn'   => 'test',
        'pms.test.axhome.com.cn'     => 'test',
        'h.test.axhome.com.cn'       => 'test',
        'account.test.axhome.com.cn' => 'test',
        'report.test.axhome.com.cn'  => 'test',
    ],
    'SITEURL'          => 'http://oa.anxin.chenmm.cn/',//项目首页地址
    'LOG_STATUS'       => true,
    'LOG_PATH'         => '/dev/shm/anxintest.cutlog',//日志路径
    'LOG_PATH_ERROR'   => '/dev/shm/anxintest.cutlog',//错误日志路径
    'LOG_PATH_SQL'     => '/dev/shm/anxintest.cutlog',//错误日志路径
    //日志配置
    //自定义部分结束
    //BASE
    'MEMCACHE_DEFAULT' => [
        "ip"         => '127.0.0.1',
        "port"       => 11311,
        'key_pre'    => 'testanxin_',
        'max_expire' => 259200//3天
    ],
    'MEMCACHE_LOCAL'   => [
        "ip"         => '127.0.0.1',
        "port"       => 11311,
        'key_pre'    => 'testanxin_',
        'max_expire' => 259200//3天
    ],
    'COOKIE_DOMAIN'    => '.axhome.com.cn',
    //数据库配置
    'DB'               => [
        'type'     => 'Pdo',
        'driver'   => 'mysql',
        'dbname'   => 'anxin_oa',
        'host'     => 'rm-bp1sa2oj35v32u1jp.mysql.rds.aliyuncs.com',
        'port'     => '3306',
        'charset'  => 'UTF8',
        'username' => 'anxin_test',
        'password' => 'anxin_21061104',
    ],

    'SENDCLOUD' => [
        'accessKey' => '281f8e197a394f11bfd2ff74c9aa7986',
        'secretKey' => 'cyMIaPuzva6FC2LjSqrOQq1vhh4UJ2cD',
    ],

    'LOGIN_CONFIG' => [
        'login_url'         => '//account.test.axhome.com.cn',
        'edit_password_url' => '//account.test.axhome.com.cn/index/editPasswd.html',
        'verify_url'        => '//account.test.axhome.com.cn/verify.html',
        'login_weixin'      => '//account.test.axhome.com.cn/?act=weixin',
        'redirect'          => '//account.test.axhome.com.cn/re.html',
    ],
    'OFFICE_ID'    => '10381',
    'DEBUG'        => true,
];