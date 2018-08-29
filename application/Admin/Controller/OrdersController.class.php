<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 10:43
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\OrderGoodsModel;
use Common\Model\OrderModel;

class OrdersController extends AdminbaseController
{
    public function index()
    {
        $post = I('post.');
        $mid =I('mid','','intval');
        //订单号
        if ($post['orderno']) {
            $where['a.orderno'] = array('like',"%".$post['orderno']."%");
            $this->assign('orderno',$post['orderno']);
        }
        if($mid >0)
        {
            $where['a.member_id'] =$mid;
            $this->assign('mid',$mid);
        }
        //状态
        if ($post['status']) {
            $where['a.status'] = $post['status'];
            $this->assign('status',$post['status']);
        }

        //支付方式
        if ($post['paytype']) {
            $where['a.paytype'] = $post['paytype'];
            $this->assign('paytype',$post['paytype']);
        }

        //订单时间
        if ($post['start_time'] || $post['end_time']) {
            $where['a.create_time'] = array('between',[strtotime($post['start_time']),strtotime($post['end_time'])]);
            $this->assign('start_time',$post['start_time']);
            $this->assign('end_time',$post['end_time']);
        }

        $orderModel = new OrderModel();
        $count = count($orderModel->getOrders($where,''));
        $page = $this->page($count, 20);
        $orders = $orderModel->getOrders($where,$page);
        foreach ($orders as &$v)
        {
            $OGModel = new OrderGoodsModel();
            $goods = $OGModel->where(['order_id'=>$v['id']])->select();
            //获取总商品数量
            $v['number'] = array_sum(array_column($goods,'number'));
            //排列商品名称
            $names = array_column($goods,'goodsname');
            foreach ($names as $k=>$j)
            {
                if ($k == 0) {
                    $v['goods_name'] .= $j;
                }else{
                    $v['goods_name'] .= '/'.$j;
                }
            }
        }

        $this->assign('list',$orders);
        $this->assign('where',$where);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    //更变订单状态
    public function change_status()
    {
        $post = I('post.');
        $orderModel = new OrderModel();
        if ($post['status'] == 4) {
            $condition['send_time'] = time();
        }else{
            $condition['receive_time'] = time();
        }

        $condition['status'] = $post['status'];
        $rst = $orderModel->where(['id'=>$post['id']])->save($condition);
        if ($rst != false) {
            $this->ajaxReturn(['code'=>1,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['code'=>0,'msg'=>'操作失败']);
        }
    }

    //赊账还款
    public function refund()
    {
        $post = I('post.');
        $orderModel = new OrderModel();
        $rst = $orderModel->where(['id'=>$post['id']])->save(['paytype'=>$post['paytype'],'pay_time'=>time()]);
        if ($rst != false) {
            $this->ajaxReturn(['code'=>1,'msg'=>'操作成功']);
        }else{
            $this->ajaxReturn(['code'=>0,'msg'=>'操作失败']);
        }
    }

    //导出EXCEL
    public function export()
    {
        $get = I('get.');
        //数据库中的数据表
        $xlsName = "Stamp";
        $xlsCell = array(
            array('id', '编号'),
            array('create_time', '时间'),
            array('orderno', '订单号'),
            array('goods_name', '商品名称'),
            array('number', '数量'),
            array('member_id', '购买者'),
            array('paymoney', '实付金额'),
            array('paytype', '支付方式'),
            array('status', '状态'),
        );

        //订单号
        if ($get['orderno']) {
            $where['a.orderno'] = array('like',"%".$get['orderno']."%");
        }

        //状态
        if ($get['status']) {
            $where['a.status'] = $get['status'];
        }

        //支付方式
        if ($get['paytype']) {
            $where['a.paytype'] = $get['paytype'];
        }

        if ($get['member_id']) {
            $where['a.member_id'] = $get['member_id'];
        }

        //订单时间
        if ($get['start_time'] || $get['end_time']) {
            $where['a.create_time'] = array('between',[strtotime($get['start_time']),strtotime($get['end_time'])]);
        }

        $xlsModel = new OrderModel();
        //导出所有的内容
        $xlsData = $xlsModel->getOrders($where,'');
        foreach ($xlsData as &$v)
        {
            $OGModel = new OrderGoodsModel();
            $goods = $OGModel->where(['order_id'=>$v['id']])->select();
            $v['number'] = array_sum(array_column($goods,'number'));
            $names = array_column($goods,'goodsname');
            foreach ($names as $k=>$j)
            {
                if ($k == 0) {
                    $v['goods_name'] .= $j;
                }else{
                    $v['goods_name'] .= '/'.$j;
                }

            }
        }

        //excel里面字段处理存表
        foreach($xlsData as &$v){
            //时间处理
            $v['create_time']=date("Y-m-d H:i:s",$v['create_time']);

            //购买者
            $member = M('member')->where(['id'=>$v['member_id']])->find();
            $v['member_id'] = $member['nickname'].'/'.$member['mobile'];

            //支付方式
            switch ($v['paytype'])
            {
                case 1:
                    $v['paytype'] = '微信支付';
                    break;
                case 2:
                    $v['paytype'] = '支付宝支付';
                    break;
                case 3:
                    $v['paytype'] = '余额支付';
                    break;
                case 4:
                    $v['paytype'] = '赊账';
                    break;
                case 5:
                    $v['paytype'] = '赊账/已还款';
                    break;
            }

            //状态
            switch ($v['status'])
            {
                case 1:
                    $v['status'] = '已完成';
                    break;
                case 2:
                    $v['status'] = '待支付';
                    break;
                case 3:
                    $v['status'] = '待发货';
                    break;
                case 4:
                    $v['status'] = '待收货';
                    break;
            }
        }
        /* 调用导出方法 */
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }

    //订单详情
    public function detail()
    {
        $id = I('get.id');

        $order = D('order')->where(['id'=>$id])->find();
        $member = D('member')->where(['id'=>$order['member_id']])->find();

        $where['order_id'] = $order['id'];
        $OGModel = new OrderGoodsModel();
        $goodsInfo = $OGModel->getGoods($where,'');

        $this->assign('order',$order);
        $this->assign('member',$member);
        $this->assign('goods',$goodsInfo);
        $this->display();
    }

}