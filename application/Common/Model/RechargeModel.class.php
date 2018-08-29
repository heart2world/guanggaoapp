<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 16:30
 */

namespace Common\Model;


class RechargeModel extends CommentsModel
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
    public function getRecharge($where,$page)
    {
        $data =  M('recharge a')
            ->join(C('DB_PREFIX').'member b on a.member_id = b.id')
            ->field('a.*,b.nickname,b.mobile')
            ->where($where)
            ->order("a.create_time DESC")
            ->limit($page->firstRow, $page->listRows)
            ->select();

        return $data;
    }
}