<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/21
 * Time: 22:34
 */

namespace Clhapp\Home\Action;

use Clhapp\Extend\HeartBeat;
use Clhapp\Log;
use Clhapp\Sync\CjtSync;
use Clhapp\Sync\YdhSync;

class CrontabAction extends MainAction
{
    /**
     * _init @desc
     *
     * @author wangjian
     */
    protected function _init()
    {
        if (!IS_CLI) {
            die();
        }

        C('LOG_PATH_SQL', '/dev/shm/anxin_crontab_sql.cutlog');
    }

    /**
     * heartbeat @desc 发送心跳
     *
     * @author wangjian
     */
    public function heartbeat()
    {
        Log::write('heartbeat', 'heartbeat');
        HeartBeat::run();
    }

    /**
     * syncCjtSaleOrder @desc 定时同步畅捷通销售订单
     *
     * @author wangjian
     */
    public function syncCjtSaleOrder()
    {
        CjtSync::syncSaleOrder();
    }

    /**
     * syncCjtMember @desc 定时同步畅捷通会员
     *
     * @author wangjian
     */
    public function syncCjtMember()
    {
        CjtSync::syncMember();
    }

    /**
     * syncCjtPartner @desc 定时同步畅捷通客户
     *
     * @author wangjian
     */
    public function syncCjtPartner()
    {
        CjtSync::syncPartner();
    }

    /**
     * syncYdhGood @desc 定时同步易订货商品
     *
     * @author wangjian
     */
    public function syncYdhGood()
    {
        YdhSync::syncGood();
    }

    /**
     * syncYdhCustomer @desc 定时同步易订货客户
     *
     * @author wangjian
     */
    public function syncYdhCustomer()
    {
        YdhSync::syncCustomer();
    }

    /**
     * syncYdhLogisticsDeliver @desc 定时同步易订货出库、发货单
     *
     * @author wangjian
     */
    public function syncYdhLogisticsDeliver()
    {
        YdhSync::syncLogisticsDeliver();
    }
}