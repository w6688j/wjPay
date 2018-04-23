<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/21
 * Time: 1:03
 */

namespace Clhapp\Cjt;

class DealCjt
{
    /**
     * inventoryQuery @desc 存货查询
     *
     * @author wangjian
     *
     * @param string $Ts 时间戳
     *
     * @return mixed
     */
    static public function inventoryQuery($Ts = '')
    {
        $params = [
            'PageSize'     => CjtApi::LIST_SIZE,
            'SelectFields' => 'Code,Name,Shorthand,Specification,InventoryClass.Code,InventoryClass.Name,Unit.Code,Unit.Name,ProductInfo.Code,ProductInfo.Name,InvSCost,AvagCost,Disabled,Ts,ImageList,DefaultBarCode,IsSale,InvBarCodeDTOs.Code,InvBarCodeDTOs.Name',
        ];
        $Ts && $params['Ts'] = $Ts;

        return (new CjtInventoryApi())
            ->setParams('param', $params)
            ->inventoryQuery();
    }

    /**
     * inventoryClassQuery @desc 存货分类查询
     *
     * @author wangjian
     * @return mixed
     */
    static public function inventoryClassQuery()
    {
        return (new CjtInventoryApi())
            ->setParams('param', ['SelectFields' => 'ID,Code,Name,ParentCode,Ts'])
            ->inventoryClassQuery();
    }

    /**
     * InventoryCreate @desc 存货创建
     *
     * @author wangjian
     *
     * @param array $params
     *
     * @return mixed
     */
    static public function InventoryCreate($params = [])
    {
        return (new CjtInventoryApi())
            //String 存货编码 "01001"
            ->setParams('Code', $params['Code'])
            //String 存货名称 "中南海1mg"
            ->setParams('Name', $params['Name'])
            //String 助记码 "ZNH1MG"
            ->setParams('Shorthand', $params['Shorthand'])
            //String 规格型号 "1mg"
            ->setParams('Specification', $params['Specification'])
            //String 默认条码 "0100101"
            ->setParams('DefaultBarCode', $params['DefaultBarCode'])
            //InventoryClassDTO 存货分类 {Code:"01",Name:"香烟"} 传入数组 如：['Code'=>'01','Name'=>'香烟']
            ->setParams('InventoryClass', $params['InventoryClass'])
            //Enum 品牌 {Code:"01",Name:"中南海"}
            ->setParams('ProductInfo', $params['ProductInfo'])
            //UnitDTO 主计量单位 {Code:"000",Name:"盒"}
            ->setParams('Unit', $params['Unit'])
            //String 外购属性 "True"
            ->setParams('IsPurchase', $params['IsPurchase'])
            //String 销售属性 "True"
            ->setParams('IsSale', $params['IsSale'])
            //String 自制属性 "True"
            ->setParams('IsMadeSelf', $params['IsMadeSelf'])
            //String 生产耗用属性 "True"
            ->setParams('IsMaterial', $params['IsMaterial'])
            //String 成套件属性 "True"
            ->setParams('IsSuite', $params['IsSuite'])
            //String 劳务属性 "True"
            ->setParams('IsLaborCost', $params['IsLaborCost'])
            //以下为可选参数
            /*//UnitDTO 销售常用单位 {Name:"盒"}
            ->setParams('UnitBySale', $params['UnitBySale'])
            //UnitDTO 零售常用单位 {Name:"盒"}
            ->setParams('UnitByRetail', $params['UnitByRetail'])
            //UnitDTO 采购常用单位  {Name:"盒"}
            ->setParams('UnitByPurchase', $params['UnitByPurchase'])
            //UnitDTO 库存常用单位 {Name:"盒"}
            ->setParams('UnitByStock', $params['UnitByStock'])
            //UnitDTO 生产常用单位 {Name:"盒"}
            ->setParams('UnitByManufacture', $params['UnitByManufacture'])
            //enum 税率 {Name:"17"}
            ->setParams('TaxRate', $params['TaxRate'])
            //Number 参考成本 100
            ->setParams('InvSCost', $params['InvSCost'])
            //Number 最新成本 96.2
            ->setParams('LatestCost', $params['LatestCost'])
            //Number 平均成本 95.16
            ->setParams('AvagCost', $params['AvagCost'])
            //Number 保质期 30
            ->setParams('Expired', $params['Expired'])
            //Enum 保质期单位 {Code:"01",Nam:"天"}或{Code:"02",Name:"月"}
            ->setParams('ExpiredUnit', $params['ExpiredUnit'])*/
            ->inventoryCreate();
    }

    /**
     * currentStockQuery @desc 现存量查询
     *
     * @author wangjian
     *
     * @param array $params 参数
     *
     * @return mixed
     */
    static public function currentStockQuery($params = [])
    {
        return (new CjtInventoryApi())
            ->setParams('Warehouse', $params['Warehouse'])
            ->setParams('InvBarCode', $params['InvBarCode'])
            ->setParams('BeginInventoryCode', $params['BeginInventoryCode'])
            ->setParams('EndInventoryCode', $params['EndInventoryCode'])
            ->setParams('InventoryName', $params['InventoryName'])
            ->setParams('Specification', $params['Specification'])
            ->setParams('Brand', $params['Brand'])
            ->setParams('GroupInfo', $params['GroupInfo'])
            ->currentStockQuery();
    }

    /**
     * orderCreate @desc 创建采购订单
     *
     * @author wangjian
     *
     * @param array $params 提交参数
     *
     * @return mixed
     */
    static public function orderCreate($params = [])
    {
        return (new CjtInventoryApi())
            //外部系统数据编号；OpenAPI调用者填写,后台做唯一性检查。用于防止重复提交，和外系统数据对应
            ->setParams('ExternalCode', $params['ExternalCode'])
            //行号，从1开始自增长
            ->setParams('Code', $params['Code'])
            //单据日期
            ->setParams('VoucherDate', $params['VoucherDate'])
            //单据明细信
            ->setParams('VoucherDetails', $params['VoucherDetails'])
            //业务类型。{Code:"01"},取值范围：01普通采购02采购退货
            ->setParams('BusiType', $params['BusiType'])
            //往来单位，格式{Code:“001”}传入与T+系统编码一致
            ->setParams('Partner', $params['Partner'])
            //仓库信息。传入的仓库编码信息与T+系统编码一致
            ->setParams('Warehouse', $params['Warehouse'])
            //存货，传入的存货编码信息与T+系统编码一致，与条形码不能同时为空
            ->setParams('Inventory', $params['Inventory'])
            //主计量单位数量
            ->setParams('BaseQuantity', $params['BaseQuantity'])
            //以下为选填项
            //备注
            ->setParams('Memo', $params['Memo'])
            //部门
            ->setParams('Department', $params['Department'])
            //经手人
            ->setParams('Clerk', $params['Clerk'])
            //表头自定义项列：分别为公用数值自定义项（pubuserdefdecm），公用字符自定义项（pubuserdefnvc），私有数值 （priuserdefdecm），字符（priuserdefnvc）。各6个（1~6），总24个
            ->setParams('DynamicPropertyKeys', $params['DynamicPropertyKeys'])
            //表头自定义项值,DynamicPropertyKeys相对应值
            ->setParams('DynamicPropertyValues', $params['DynamicPropertyValues'])
            //表体项目
            ->setParams('Project', $params['Project'])
            //失效日期，可不录，选项批号自动带出时，带出该值。如果已录批号，存货启用有效期管理，不能为空
            ->setParams('ExpiryDate', $params['ExpiryDate'])
            //批号，可不录，有批号自动带出时，带出，并且带出相关信息
            ->setParams('Batch', $params['Batch'])
            //成本金额
            ->setParams('Amount', $params['Amount'])
            //成本单价
            ->setParams('Price', $params['Price'])
            //辅助计量单位数量，浮动计量时不能为空
            ->setParams('SubQuantity', $params['SubQuantity'])
            ->orderCreate();
    }

    /**
     * saleOrderCreate @desc 新增销售订单
     *
     * @author wangjian
     *
     * @param array $params 提交参数
     *
     * @return mixed
     */
    static public function saleOrderCreate($params = [])
    {
        return (new CjtSaleOrderApi())
            ->setParams('ExternalCode', $params['ExternalCode'])
            ->setParams('ReciveType', $params['ReciveType'])
            ->setParams('Code', $params['Code'])
            ->setParams('Customer', $params['Customer'])
            //->setParams('SettleCustomer', $params['SettleCustomer'])
            ->setParams('Warehouse', $params['Warehouse'])
            //->setParams('Department', $params['Department'])
            //->setParams('Clerk', $params['Clerk'])
            //->setParams('Project', $params['Project'])
            //->setParams('Currency', $params['Currency'])
            //->setParams('ExchangeRate', $params['ExchangeRate'])
            ->setParams('DeliveryDate', $params['DeliveryDate'])
            //->setParams('ContractCode', $params['ContractCode'])
            ->setParams('Address', $params['Address'])
            ->setParams('LinkMan', $params['LinkMan'])
            ->setParams('ContactPhone', $params['ContactPhone'])
            ->setParams('Maker', $params['Maker'])
            ->setParams('Memo', $params['Memo'])
            //->setParams('Member', $params['Member'])
            //->setParams('IsAutoAudit', $params['IsAutoAudit'])
            //->setParams('DataSource', $params['DataSource'])
            //->setParams('OrigEarnestMoney', $params['OrigEarnestMoney'])
            //->setParams('Subscriptions', $params['Subscriptions'])
            ->setParams('SaleOrderDetails', $params['SaleOrderDetails'])
            ->create();
    }

    /**
     * memberCreate @desc 创建会员
     *
     * @author wangjian
     *
     * @param array $params 提交参数
     *
     * @return mixed
     */
    static public function memberCreate($params = [])
    {
        return (new CjtMemberApi())
            ->setParams('Code', $params['Code'])
            ->setParams('CardCode', $params['CardCode'])
            ->setParams('Name', $params['Name'])
            ->setParams('MemberType', $params['MemberType'])
            ->setParams('Mobilephone', $params['Mobilephone'])
            ->createMember();
    }

    /**
     * partnerCreate @desc 往来单位创建
     *
     * @author wangjian
     *
     * @param array $params 提交参数
     *
     * @return mixed
     */
    static public function partnerCreate($params = [])
    {
        return (new CjtPartnerApi())
            ->setParams('Code', $params['Code'])
            ->setParams('Name', $params['Name'])
            ->setParams('PartnerType', $params['PartnerType'])
            ->setParams('PartnerClass', $params['PartnerClass'])
            ->setParams('Disabled', $params['Disabled'])
            ->setParams('CustomerAddressPhone', $params['CustomerAddressPhone'])
            ->setParams('PartnerAddresDTOs', $params['PartnerAddresDTOs'])
            ->createPartner();
    }

    /**
     * partnerQuery @desc 往来单位查询
     *
     * @author wangjian
     *
     * @param string $Ts 时间戳
     *
     * @return mixed
     */
    static public function partnerQuery($Ts = '')
    {
        $params = [
            'PageSize'     => CjtApi::LIST_SIZE,
            'SelectFields' => 'Code,Name,Shorthand,PartnerAbbName,PartnerType.Code,PartnerType.Name,PartnerClass.Code,PartnerClass.Name,District.Code,District.Name,Disabled,ARBalance,AdvRBalance,APBalance,AdvPBalance,Ts,PartnerAddresDTOs.Code,PartnerAddresDTOs.Name,PartnerAddresDTOs.Contact,PartnerAddresDTOs.MobilePhone,PartnerAddresDTOs.Fax,PartnerAddresDTOs.EmailAddr,PartnerAddresDTOs.QqNo,PartnerAddresDTOs.AddressJc,PartnerAddresDTOs.ShipmentAddress,PartnerAddresDTOs.Position',
        ];
        $Ts && $params['Ts'] = $Ts;

        return (new CjtPartnerApi())
            ->setParams('param', $params)
            ->partnerQuery();
    }

    /**
     * saleDispatchQuery @desc 销货出库单查询
     *
     * @author wangjian
     * @return mixed
     */
    static public function saleDispatchQuery()
    {
        return (new CjtSaleDispatchApi())
            ->setParams('param', [])
            ->saleDispatchQuery();
    }

    /**
     * SaleDeliveryQueryExecuting @desc 销货单执行情况查询接口
     *
     * @author wangjian
     * @return mixed
     */
    static public function SaleDeliveryQueryExecuting()
    {
        return (new CjtSaleDeliveryApi())
            ->setParams('queryParam', [])
            ->saleDeliveryQueryExecuting();
    }
}