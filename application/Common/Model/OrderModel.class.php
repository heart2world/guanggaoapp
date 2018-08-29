<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 11:14
 */

namespace Common\Model;


class OrderModel extends CommentsModel
{
    protected $_auto = array(
        array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
    );

    //用于获取时间，格式为时间戳,注意,方法不能为private
    function mGetDate() {
        return time();
    }

    protected function _before_write(&$data) {
        parent::_before_write($data);
    }

    /**
     * @param $where
     * @param $page
     * @return mixed
     * 根据条件按照时间排序返回订单列表
     */
    public function getOrders($where,$page)
    {
       $data =  M('order a')
           ->join(C('DB_PREFIX').'member b on a.member_id = b.id')
           ->field('a.*,b.nickname,b.mobile,b.remark1')
           ->where($where)
           ->order("a.create_time DESC")
           ->limit($page->firstRow, $page->listRows)
           ->select();

       return $data;
    }

    /**
     * @param $where
     * @param $page
     * @return mixed
     * 根据条件获取销量数据
     */
    public function getSales($where,$page)
    {
        $data = M('goods a')
            ->join(C('DB_PREFIX').'order_goods b on b.goods_id = a.id')
            ->join(C('DB_PREFIX').'order c on b.order_id = c.id')
            ->field('sum(b.total_price) paymoney,(sum(b.total_price) - sum(b.number)*a.settleprice) profit,b.goodsname,sum(b.number) number')
            ->where($where)
            ->group('b.goods_id')
            ->order('paymoney desc')
            ->limit($page->firstRow, $page->listRows)
            ->select();
        return $data;
    }

    /**
     * 创建订单
     * @param $data
     * @return mixed
     */
    public function add_order($data)
    {
        $address = json_decode(htmlspecialchars_decode($data['address']),true);
        $param['orderno'] = $data['orderno'];
        $param['member_id'] = $data['uid'];
        $param['amount'] = $data['amount'];
        $param['paymoney'] = $data['paymoney'];
        $param['province'] = $address['province'];
        $param['city'] = $address['city'];
        $param['district'] = $address['district'];
        $param['street'] = $address['street'];
        $param['detail_address'] = $address['address'];
        $param['receive_name'] = $address['linkman'];
        $param['receive_mobile'] = $address['mobile'];
        $param['remark'] = $data['remark'];
        $param['status'] = 2;
        $param['create_time'] = time();
        return $this->add($param);
    }

    /**
     * 删除订单
     * @param $order_id
     * @return mixed
     */
    public function delete_order($order_id)
    {
        return $this->where(['id'=>$order_id])->delete();
    }

    /**
     * 更改订单状态
     * @param $where
     * @param $condition
     * @return bool
     */
    public function edit_order($where,$condition)
    {
        return $this->where($where)->save($condition);
    }
}