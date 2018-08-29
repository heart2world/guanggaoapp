<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 11:27
 */

namespace Common\Model;


class OrderGoodsModel extends CommentsModel
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


    public function getGoods($where,$page)
    {
        $data = M('order_goods')
            ->where($where)
            ->order('id desc')
            ->limit($page->firstRow, $page->listRows)
            ->select();
        return $data;
    }

    public function addGoods($data)
    {
        $data['specification'] = $data['goodsbrief'];
        $data['create_time'] = time();
        return $this->add($data);
    }
}