<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 14:08
 */

namespace Common\Model;


class GoodsModel extends CommentsModel
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
        $data = M('goods a')
            ->join(C('DB_PREFIX').'order_goods b on b.goods_id = a.id')
            ->join(C('DB_PREFIX').'order c on b.order_id = c.id')
            ->field('a.goodsname,a.clickcount,sum(b.number) number,(b.number/a.clickcount) cvr')
            ->where($where)
            ->order('cvr desc')
            ->group('a.id')
            ->limit($page->firstRow, $page->listRows)
            ->select();

        return $data;
    }
}