<?php
/**
 * Created by PhpStorm.
 * User: wangjian
 * Date: 2017/2/21
 * Time: 23:15
 */
return [
    //定时任务状态
    'php_bin'     => '/usr/local/php7/bin/php',
    'log'         => '/dev/shm/crontab.log',
    'pid_path'    => '/tmp/crontab.pid',
    'run_file'    => VPATH . '/wwwroot/Home/crontab.php',
    'status'      => true,
    //心跳间隔时间 间隔调整 每3小时发一次心跳
    'heart_space' => 86400,
    'tasks'       => [
        //每6小时服务保证
        //['0 */6 * * * ', 'heartbeat', '每6小时一次心跳'],
        //同步易订货
        ['*/5 * * * * ', 'syncYdhGood', '每5分钟同步易订货商品'],
        ['*/5 * * * * ', 'syncYdhCustomer', '每5分钟同步易订货客户'],
        //同步畅捷通
        ['*/5 * * * * ', 'syncCjtSaleOrder', '每5分钟同步畅捷通销售订单'],
        ['*/5 * * * * ', 'syncYdhLogisticsDeliver', '每5分钟同步易订货出库、发货单'],
    ],
];