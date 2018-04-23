<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/2
 * Time: 11:48
 */

return [
    'VERSION' => '20161130',//版本号 程序版本

    'HOST_CONFIG' => [
        'default' => 'default',
    ],

    'SSL'                => false,
    'COOKIE_DOMAIN'      => '.axhome.com.cn',
    //页面信息配置
    'WEB_TITLE'          => '安心OA管理',//页面标题
    'WEB_DESC'           => '安心OA管理', //页面描述
    'APP_HOST'           => 'dev.izhuyan.com',//该网址默认域名
    'SITEURL'            => 'http://dev.izhuyan.com/',//项目首页地址
    'APILOG_PATH'        => '/dev/shm/api.cutlog',
    //日志配置
    'LOG_STATUS'         => true, //日志开关
    'LOG_PATH'           => '/dev/shm/anxin.cutlog',//日志路径
    'LOG_PATH_ERROR'     => '/dev/shm/anxin_error.cutlog',//错误日志路径
    'LOG_PATH_SQL'       => '/dev/shm/anxin_sql.cutlog',//错误日志路径
    'LOG_PATH_PARSE_SQL' => '/dev/shm/parse_sql.cutlog',
    'MODULE_REPLACE'     => [
        //模板全局替换内容
        '__PUBLIC__'         => 'http://oa.axhome.com.cn/style',
        '__JS_DIR__'         => 'http://oa.axhome.com.cn/style/js',
        '__CSS_DIR__'        => 'http://oa.axhome.com.cn/style/css',
        '__DF_FACE__'        => 'http://oa.axhome.com.cn/style/face.png',
        '__IMGHOLDER__'      => 'http://usr.im/',
        '__STATIC_IMAGES__'  => 'http://static.sdxapp.com/images',
        '__STATIC_JS_DIR__'  => 'http://static.sdxapp.com/anxin/js',
        '__STATIC_CSS_DIR__' => 'http://static.sdxapp.com/anxin/css',
        '__RESOURCE__'       => 'http://static.sdxapp.com/style',
    ],

    //自定义部分结束
    //BASE
    'AUTH_KEY'           => 'sdxapp_anxinauth',

    'SHOW_SQL'  => true,//是否记录SQL
    'LOG_TRACE' => true,//强制记录调试语句入日志

    'APP_LIST' => 'Home,Home',

    'NOLOGIN_MODULE'    => 'login,crontab',
    'NOLOGIN_ACTION'    => '',
    //无需登录的操作
    'ERROR_PAGE'        => '',//错误页面地址
    '404_PAGE'          => '',//404页面地址
    'MODEL_PATH'        => 'Model',//模型类库 路径
    'DISPATCH_JUMP_TPL' => 'Public/dispatchJump.html',

    'TEMPLATE_EXT'       => '.html',

    //路由
    'URL_MODEL'          => 2,//1:兼容模式 2：重写模式
    'MODULE_LAYER'       => 'm',
    'ACTION_LAYER'       => 'a',
    'DEFAULT_MODULE'     => 'index',
    'DEFAULT_ACTION'     => 'index',
    'DEFAULT_CHARSET'    => 'UTF-8',
    'LIB_PATH'           => 'Libs/classes',//类库目录
    'TPL_PREFIX'         => '.html',//默认模板后缀名称
    'TMPL_CONTENT_TYPE'  => 'text/html',
    'HTTP_CACHE_CONTROL' => 'private',  // 网页缓存控制
    //调试信息

    'CACHE_DIR'        => '/tmp/dev.izhuyan.com/',//文件缓存目录

    //memcache 实例
    'MEMCACHE_DEFAULT' => [
        "ip"         => '127.0.0.1',
        "port"       => 11311,
        'key_pre'    => 'oaanxin_',
        'max_expire' => 259200//3天
    ],

    //数据库配置
    'DB'               => [
        'type'     => 'Pdo',
        'driver'   => 'mysql',
        'dbname'   => 'dev',
        'host'     => '116.62.40.33',
        'port'     => '3306',
        'charset'  => 'UTF8',
        'username' => 'root',
        'password' => 'WJ_CENTOS',
    ],

    'IMAGE' => [
        'type'              => 'oss',
        'domain'            => 'img.axhome.com.cn',
        'access_key_id'     => 'LTAIaGWidTOAESgd',
        'access_secret_key' => '5QAQyFa7me2R59fOBmX91w3N3wA4tQ',
        'end_point'         => 'oss-cn-hangzhou-internal.aliyuncs.com',
        'bucket'            => 'sdx-anxin',
        'legal_dir'         => [
            'axface',//人员头像
            'axproject',//人员头像
            'card',//负责人名片
            'counterSigned',
            'idcard',
            'user_face',
            'axapp',
        ],
    ],

    'CTY_USER_NAME'   => 'shxsdz-1',
    'CTY_USER_PASSWD' => '348dce',

    'LOGIN_CONFIG'     => [
        'login_url'         => '//account.axhome.com.cn',
        'edit_password_url' => '//account.axhome.com.cn/index/editPasswd.html',
        'verify_url'        => '//account.axhome.com.cn/verify.html',
        'login_weixin'      => '//account.axhome.com.cn/?act=weixin',
        'redirect'          => '//account.axhome.com.cn/re.html',
    ],
    'SENDCLOUD'        => [
        'accessKey' => '281f8e197a394f11bfd2ff74c9aa7986',
        'secretKey' => 'cyMIaPuzva6FC2LjSqrOQq1vhh4UJ2cD',
    ],
    'OFFICE_ID'        => '10382',
    //测试机ip
    'TEST_SERVER_FILE' => VPATH . '/test',
    'CLASS_MAP'        => [
        'mmxs\\wxcorp' => 'wxcorp',
    ],

    'DEBUG' => true,
];