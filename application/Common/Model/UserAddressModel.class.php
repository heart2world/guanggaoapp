<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 10:21
 */

namespace Common\Model;


class UserAddressModel extends CommonModel
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

    //返回用户所有地址
    public function getAddress($id)
    {
        return $this->where(['user_id'=>$id])->order('id desc,isdefault desc')->select();
    }

    //添加地址
    public function addAddress($data)
    {
        $arr = explode(' ',$data['pcd']);
        $data['province'] = $arr[0];
        $data['city'] = $arr[1];
        $data['district'] = $arr[2];
        $data['user_id'] = $data['uid'];
        $data['create_time'] = time();
        return $this->add($data);
    }

    //编辑地址
    public function editAddress($data)
    {
        if ($data['pcd']) {
            $arr = explode(' ',$data['pcd']);
            $data['province'] = $arr[0];
            $data['city'] = $arr[1];
            $data['district'] = $arr[2];
        }

        if ($data['isdefault'] == 1) {
            $this->where(['isdefault'=>1])->save(['isdefault'=>0]);
        }

        return $this->where(['id'=>$data['id']])->save($data);
    }
}