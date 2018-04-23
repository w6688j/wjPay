<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/2
 * Time: 21:29
 */

namespace Clhapp\Ydh;

class DealYdh
{
    /**
     * pullOrder @desc 订单拉取
     *
     * @author wangjian
     *
     * @param int    $size          拉取订单数量，单次不能超100
     * @param bool   $loadLog       获取订单的时候，是否获取订单的操作日志
     * @param bool   $loadDetail    获取订单的时候，是否获取订单的详情
     * @param bool   $loadRemark    获取订单的时候，是否获取订单的备注
     * @param string $transportType 可以传tcp和udp，如果选择了tcp那拉取的订单需要显式地告诉服务器已经收到该订单，然后服务器会把这个订单从拉取队列里面删除，udp模式就从拉取瞬间就从队列里面删除，调用接口pull_order_status接口告诉服务器拉取结果
     *
     * @return array
     */
    static public function pullOrder($size = YdhOrderApi::SIZE, $loadLog = false, $loadDetail = true, $loadRemark = true, $transportType = YdhOrderApi::TRANSPORT_TYPE_UDP)
    {
        return (new YdhOrderApi())
            ->setParams('size', $size)
            ->setParams('loadLog', $loadLog)
            ->setParams('loadDetail', $loadDetail)
            ->setParams('loadRemark', $loadRemark)
            ->setParams('transportType', $transportType)
            ->setParams('listType', 'limit')
            ->setParams('currentPage', 1)
            ->pullOrder();
    }

    /**
     * orderAllList @desc
     *
     * @author wangjian
     *
     * @param array  $where       查询条件
     * @param int    $currentPage 当前分页码
     * @param string $loadLog     获取订单的时候，是否获取订单的操作日志
     * @param string $loadDetail  获取订单的时候，是否获取订单的详情
     * @param string $loadRemark  获取订单的时候，是否获取订单的备注
     * @param int    $size        分页大小
     *
     * @return array
     */
    static public function orderAllList($where = [], $currentPage = 1, $loadLog = 'false', $loadDetail = 'true', $loadRemark = 'true', $size = YdhApi::LIST_SIZE)
    {
        $Obj = (new YdhOrderApi());
        $Obj
            ->setParams('pageSize', $size)
            ->setParams('currentPage', $currentPage)
            ->setParams('loadLog', $loadLog)
            ->setParams('loadDetail', $loadDetail)
            ->setParams('loadRemark', $loadRemark);

        $where['keyword'] && $Obj->setParams('keyword', $where['keyword']);
        $where['beginDate'] && $Obj->setParams('beginDate', $where['beginDate']);
        $where['endDate'] && $Obj->setParams('endDate', $where['endDate']);
        $where['isDiscountOrder'] && $Obj->setParams('isDiscountOrder', $where['isDiscountOrder']);
        $where['status'] && $Obj->setParams('status', $where['status']);
        $where['since_time'] && $Obj->setParams('since_time', $where['since_time']);
        $where['max_time'] && $Obj->setParams('max_time', $where['max_time']);
        $where['since_audit'] && $Obj->setParams('since_audit', $where['since_audit']);
        $where['max_audit'] && $Obj->setParams('max_audit', $where['max_audit']);

        return $Obj->orderAllList();
    }

    /**
     * goodCreate @desc 新增商品
     *
     * @author wangjian
     *
     * @param array $good 商品数据
     *
     * @return mixed
     */
    static public function goodCreate($good = [])
    {
        $obj = new YdhGoodApi();
        $obj
            ->setParams('name', (string)$good['name'])
            ->setParams('code', (string)$good['code'])
            ->setParams('spec', (string)$good['spec'])
            ->setParams('productTypeId', (string)$good['productTypeId'])
            ->setParams('productUnitId', (string)$good['productUnitId'])
            ->setParams('productUnitName', (string)$good['productUnitName'])
            ->setParams('marketPrice', round($good['marketPrice'], 2))
            ->setParams('orderPrice', round($good['orderPrice'], 2))
            ->setParams('imgUrl', (string)$good['imgUrl'])
            ->setParams('barcode', (string)$good['barcode']);
        $good['id'] > 0 && $obj->setParams('id', $good['id']);

        return $obj->goodCreate();
    }

    /**
     * goodInventory @desc 商品库存修改
     *
     * @author wangjian
     *
     * @param array $inventory 库存数据
     *
     * @return mixed
     */
    static public function goodInventory($inventory = [])
    {
        return (new YdhGoodApi())
            ->setParams('goodsCode', $inventory['goodsCode'])
            ->setParams('inventory', $inventory['inventory'])
            ->inventory();
    }

    /**
     * goodList @desc 商品列表
     *
     * @author wangjian
     * @return mixed
     */
    static public function goodList($currentPage = 1, $size = YdhApi::LIST_SIZE, $listType = YdhApi::LIST_TYPE_LIMIT)
    {
        return (new YdhGoodApi())
            ->setParams('size', $size)
            ->setParams('listType', $listType)
            ->setParams('currentPage', $currentPage)
            ->goodList();
    }

    /**
     * goodTypeList @desc 商品分类列表
     *
     * @author wangjian
     * @return mixed
     */
    static public function goodTypeList()
    {
        return (new YdhGoodApi())
            ->goodTypeList();
    }

    /**
     * goodUnitList @desc 商品单位列表
     *
     * @author wangjian
     * @return mixed
     */
    static public function goodUnitList()
    {
        return (new YdhGoodApi())
            ->goodUnitList();
    }

    /**
     * customerList @desc 客户列表
     *
     * @author wangjian
     */
    static public function customerList($currentPage = 1, $size = YdhApi::LIST_SIZE, $listType = YdhApi::LIST_TYPE_LIMIT)
    {
        return (new YdhCustomerApi())
            ->setParams('size', $size)
            ->setParams('listType', $listType)
            ->setParams('currentPage', $currentPage)
            ->customerList();
    }

    /**
     * customerTypeList @desc 客户类型列表
     *
     * @author wangjian
     * @return mixed
     */
    static public function customerTypeList()
    {
        return (new YdhCustomerApi())
            ->customerTypeList();
    }

    /**
     * customerCreate @desc 新增客户
     *
     * @author wangjian
     *
     * @param array $customer 客户信息
     *
     * @return mixed
     */
    static public function customerCreate($customer = [])
    {
        return (new YdhCustomerApi())
            ->setParams('name', (string)$customer['name'])
            ->setParams('code', (string)$customer['code'])
            ->setParams('contactor', (string)$customer['contactor'])
            ->setParams('mobile', (string)$customer['mobile'])
            ->setParams('customertypeId', (string)$customer['customertypeId'])
            ->setParams('activeCustomer', (int)$customer['activeCustomer'])
            ->setParams('userName', (string)$customer['userName'])
            ->setParams('password', (string)$customer['password'])
            ->setParams('address', (string)$customer['address'])
            ->setParams('position', (string)$customer['position'])
            ->customerCreate();
    }

    /**
     * logisticsDeliver @desc 分批发货接口
     *
     * @author wangjian
     *
     * @param array $deliver 发货数组
     *
     * @return mixed
     */
    static public function logisticsDeliver($deliver = [])
    {
        return (new YdhLogisticsApi())
            //->setParams('orderNum', (string)$deliver['orderNum'])
            //->setParams('billEntries', (string)json_encode($deliver['billEntries']))
            ->setParams('billNum', (string)$deliver['billNum'])
            //->setParams('sendNumber', (string)$deliver['sendNumber'])
            //->setParams('sendDate', (string)$deliver['sendDate'])
            //->setParams('sendCompanyCode', (string)$deliver['sendCompanyCode'])
            ->logisticsDeliver();
    }

    /**
     * logisticsOutStorage @desc 分批出库接口
     *
     * @author wangjian
     *
     * @param array $outStorage 出库数组
     *
     * @return mixed
     */
    static public function logisticsOutStorage($outStorage = [])
    {
        return (new YdhLogisticsApi())
            ->setParams('orderNum', (string)$outStorage['orderNum'])
            //->setParams('billEntries', (string)json_encode($outStorage['billEntries']))
            //->setParams('billNum', (string)$outStorage['billNum'])
            //->setParams('sendNumber', (string)$outStorage['sendNumber'])
            //->setParams('sendDate', (string)$outStorage['sendDate'])
            //->setParams('sendCompanyCode', (string)$outStorage['sendCompanyCode'])
            ->logisticsOutStorage();
    }

    /**
     * queryAllLogisticsBill @desc 出库发货单详情
     *
     * @author wangjian
     *
     * @param array $params 查询条件
     *
     * @return mixed
     */
    static public function queryAllLogisticsBill($params = [])
    {
        return (new YdhLogisticsApi())
            ->setParams('orderNum', $params['orderNum'])
            ->queryAllLogisticsBill();
    }

    /**
     * orderAudit @desc 订单审核
     *
     * @author wangjian
     *
     * @param array $params
     *
     * @return mixed
     */
    static public function orderAudit($params = [])
    {
        return (new YdhOrderApi())
            ->setParams('orderNum', $params['orderNum'])
            ->setParams('version', $params['version'])
            ->setParams('returnDetail', $params['returnDetail'])
            ->orderAudit();
    }

    /**
     * orderDetail @desc 订单详情
     *
     * @author wangjian
     *
     * @param array $params
     *
     * @return mixed
     */
    static public function orderDetail($params = [])
    {
        return (new YdhOrderApi())
            ->setParams('orderNum', $params['orderNum'])
            ->orderDetail();
    }

    /**
     * warehouseList @desc 仓库列表
     * @author wangjian
     * @return mixed
     */
    static public function warehouseList()
    {
        return (new YdhInventoryApi())
            ->warehouseList();
    }
}