<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 15:37
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\RechargeConfigModel;
use Common\Model\RechargeModel;

class RechargeController extends AdminbaseController
{
    public function index()
    {
        $post = I('post.');

        //昵称
        if ($post['nickname']) {
            $where['b.nickname'] = array('like',"%".$post['nickname']."%");
            $this->assign('nickname',$post['nickname']);
        }

        //充值时间
        if ($post['start_time'] || $post['end_time']) {
            $where['a.create_time'] = array('between',[strtotime($post['start_time']),strtotime($post['end_time'])]);
            $this->assign('start_time',$post['start_time']);
            $this->assign('end_time',$post['end_time']);
        }

        $where['a.atype'] = 1;

        $rechargeModel = new RechargeModel();
        $count = count($rechargeModel->getRecharge($where,''));
        $page = $this->page($count, 20);
        $orders = $rechargeModel->getRecharge($where,$page);

        $this->assign('list',$orders);
        $this->assign('where',$where);
        $this->assign('page',$page->show('Admin'));
        $this->display();
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
            array('recharge_no', '充值单号'),
            array('member_id', '会员'),
            array('recharge_amount', '充值金额'),
            array('account_money', '实际到账'),
            array('pay_type', '支付方式'),
            array('status', '状态'),
        );

        //昵称
        if ($get['nickname']) {
            $where['b.nickname'] = array('like',"%".$get['nickname']."%");
        }

        //充值时间
        if ($get['start_time'] || $get['end_time']) {
            $where['a.create_time'] = array('between',[strtotime($get['start_time']),strtotime($get['end_time'])]);
        }

        $xlsModel = new RechargeModel();
        //导出所有的内容
        $xlsData = $xlsModel->getRecharge($where,'');
        //excel里面字段处理存表
        foreach($xlsData as &$v){
            //时间处理
            $v['create_time']=date("Y-m-d H:i:s",$v['create_time']);

            //购买者
            $member = M('member')->where(['id'=>$v['member_id']])->find();
            $v['member_id'] = $member['nickname'].'/'.$member['mobile'];

            //支付方式
            switch ($v['pay_type'])
            {
                case 1:
                    $v['pay_type'] = '微信支付';
                    break;
                case 2:
                    $v['pay_type'] = '支付宝支付';
                    break;
                default:
                    $v['pay_type'] = '—— ——';
                    break;
            }

            //状态
            switch ($v['status'])
            {
                case 1:
                    $v['status'] = '成功';
                    break;
                case 2:
                    $v['status'] = '失败';
                    break;
                default:
                    $v['status'] = '未支付';
                    break;
            }
        }
        /* 调用导出方法 */
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }

    //充值设置
    public function recharge_config()
    {
        $configModel = new RechargeConfigModel();
        $config = $configModel->find();
        $config['below_rate'] = $config['below_rate']*100;
        $config['middle_rate'] = $config['middle_rate']*100;
        $config['above_rate'] = $config['above_rate']*100;

        $this->assign('config',$config);
        $this->display();
    }

    //添加、编辑设置
    public function add_config()
    {
        $post = I('post.');
        $configModel = new RechargeConfigModel();
        $config = $configModel->find();
        if ($config) {
            $post['below_rate'] = $post['below_rate']/100;
            $post['middle_rate'] = $post['middle_rate']/100;
            $post['above_rate'] = $post['above_rate']/100;
            $rst = $configModel->where(['id'=>$config['id']])->save($post);
        }else{
            $post['create_time'] = time();
            $post['below_rate'] = $post['below_rate']/100;
            $post['middle_rate'] = $post['middle_rate']/100;
            $post['above_rate'] = $post['above_rate']/100;
            $rst = $configModel->add($post);
        }

        if ($rst) {
            $this->ajaxReturn(['code'=>1,'msg'=>'设置成功']);
        }else{
            $this->ajaxReturn(['code'=>1,'msg'=>'设置失败']);
        }

    }


    //余额明细
    public function balance_statement()
    {
        $post = I('post.');
        //昵称
        if ($post['nickname']) {
            $where['b.nickname'] = array('like',"%".$post['nickname']."%");
            $this->assign('nickname',$post['nickname']);
        }

        //充值时间
        if ($post['start_time'] || $post['end_time']) {
            $where['a.create_time'] = array('between',[strtotime($post['start_time']),strtotime($post['end_time'])]);
            $this->assign('start_time',$post['start_time']);
            $this->assign('end_time',$post['end_time']);
        }

        $where['a.status'] = 1;
        $rechargeModel = new RechargeModel();
        $count = count($rechargeModel->getRecharge($where,''));
        $page = $this->page($count, 20);
        $orders = $rechargeModel->getRecharge($where,$page);


        $this->assign('list',$orders);
        $this->assign('where',$where);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    //导出EXCEL
    public function export_balance()
    {
        $get = I('get.');
        //数据库中的数据表
        $xlsName = "Stamp";
        $xlsCell = array(
            array('id', '编号'),
            array('create_time', '时间'),
            array('member_id', '会员'),
            array('account_money', '充值变化'),
        );
        $where['a.status'] = 1;
        $xlsModel = new RechargeModel();

        //昵称
        if ($get['nickname']) {
            $where['b.nickname'] = array('like',"%".$get['nickname']."%");
        }

        //充值时间
        if ($get['start_time'] || $get['end_time']) {
            $where['a.create_time'] = array('between',[strtotime($get['start_time']),strtotime($get['end_time'])]);
        }

        //导出所有的内容
        $xlsData = $xlsModel->getRecharge($where,'');
        //excel里面字段处理存表
        foreach($xlsData as &$v){
            //时间处理
            $v['create_time']=date("Y-m-d H:i:s",$v['create_time']);

            //购买者
            $member = M('member')->where(['id'=>$v['member_id']])->find();
            $v['member_id'] = $member['nickname'].'/'.$member['mobile'];

            //支付方式
            switch ($v['atype'])
            {
                case 1:
                    $v['account_money'] = '+'.$v['account_money'];
                    break;
                case 2:
                    $v['account_money'] = '-'.$v['account_money'];
                    break;
            }
        }
        /* 调用导出方法 */
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }
}