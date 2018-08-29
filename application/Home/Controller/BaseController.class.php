<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 16:38
 */

namespace Home\Controller;


use Think\Controller;

class BaseController extends Controller
{
    function _initialize(){
        $forget = I('request.');
        if($forget['token'] != C('gg_token'))
        {
            $this->ajaxReturn(array('msg'=>'token验证失败','status'=>'201','data'=>null));
        }
    }

    //获取用户折扣
    protected function getDistrict()
    {
        $forget = I('request.');
        $discount = 1;
        $userInfo = D('member a')->join(C('DB_PREFIX').'member_role b on a.role_id = b.id')->where(['a.id'=>$forget['uid']])->find();
        if ($userInfo['persont']>0) {
            $discount = $userInfo['persont']/100;
        }

        return $discount;
    }

    //获取会员等级折扣
    protected function getLevelDistrict()
    {
        $forget = I('request.');
        $discount = 1;
        $userInfo = D('member a')->join(C('DB_PREFIX').'member_level b on a.level_id = b.id')->where(['a.id'=>$forget['uid']])->find();
        if ($userInfo['persont']>0) {
            $discount = $userInfo['persont']/10;
        }

        return $discount;
    }

    //获取支付方式
    protected function getPayType($pay_type)
    {
        switch ($pay_type)
        {
            case 1:
                return '微信支付';
                break;
            case 2:
                return '支付宝支付';
                break;
            case 3:
                return '余额支付';
                break;
            case 4:
                return '赊账';
                break;
            case 5:
                return '赊账/已还款';
                break;
        }
    }

    //获取支付状态
    protected function getOrderStatus($status)
    {
        switch ($status)
        {
            case 1:
                return '已完成';
                break;
            case 2:
                return '待支付';
                break;
            case 3:
                return '待发货';
                break;
            case 4:
                return '待收货';
                break;
        }
    }

    //提升会员等级
    protected function improveLevel($uid,$money)
    {
        $userInfo = M('member')->find($uid);
        $coin = $userInfo['coin'] + $money;
        $level_id = M('member_level')->where(['maxprice'=>['lt',$coin]])->order('maxprice desc')->getField('id');
        M('member')->where(['id'=>$userInfo['id']])->save(['coin'=>$coin,'level_id'=>$level_id]);
    }

    //增加销量、减少库存
    protected function increaseGoodsSales($order_id)
    {
        $goods = M('order_goods')->where(['order_id'=>$order_id])->select();
        foreach ($goods as $v)
        {
            if ($v['specification_id']) {
                $inventory = M('goods_option')->where(['id'=>$v['specification_id']])->getField('inventory');
                $inventory = $inventory - $v['number'];
                M('goods_option')->where(['id'=>$v['specification_id']])->save(['inventory'=>$inventory]);
            }
            
            $goods_info = M('goods')->where(['id'=>$v['goods_id']])->find();
            $goods_info['inventory'] = $goods_info['inventory'] - $v['number'];

            M('goods')->where(['id'=>$v['goods_id']])->save(['number'=>$v['number']+$goods_info['number'],'inventory'=>$goods_info['inventory']]);
        }
    }
}