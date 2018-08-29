<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 8:57
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\GoodsModel;
use Common\Model\OrderModel;

class ManagementController extends AdminbaseController
{
    public function index()
    {
        $post = I('post.');

        //时间
        if ($post['start_time'] || $post['end_time']) {
            $where['b.create_time'] = array('between',[strtotime($post['start_time']),strtotime($post['end_time'])]);
            $this->assign('start_time',$post['start_time']);
            $this->assign('end_time',$post['end_time']);
        }

        $orderModel = new OrderModel();
        $count = count($orderModel->getSales($where,''));
        $pagesize = 20;
        $page = $this->page($count, $pagesize);

        $where['c.status'] = ['neq',2];
        $data = $orderModel->getSales($where,$page);
        $total['price'] = array_sum(array_column($data,'paymoney'));
        $total['profit'] = array_sum(array_column($data,'profit'));

        $current_page = $page->getCurrentPage1();
        foreach ($data as $k=>&$v)
        {
            if ($current_page>1) {
                $v['id'] = $k+$pagesize*($current_page-1)+1;
            }else{
                $v['id'] = $k+1;
            }
        }

        $this->assign('data',$data);
        $this->assign('total',$total);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    //导出销量排行EXCEL
    public function export()
    {
        $get = I('get.');
        //数据库中的数据表
        $xlsName = "Stamp";
        $xlsCell = array(
            array('id', '排行'),
            array('goodsname', '商品名称'),
            array('number', '销量'),
            array('paymoney', '销售额'),
            array('profit', '毛利润'),
        );

        //充值时间
        if ($get['start_time'] || $get['end_time']) {
            $where['b.create_time'] = array('between',[strtotime($get['start_time']),strtotime($get['end_time'])]);
        }

        $where['c.status'] = ['neq',2];
        $xlsModel = new OrderModel();
        //导出所有的内容
        $xlsData = $xlsModel->getSales($where,'');
        foreach ($xlsData as $k=>&$v)
        {
                $v['id'] = $k+1;
        }

        $total['price'] = array_sum(array_column($xlsData,'paymoney'));
        $total['profit'] = array_sum(array_column($xlsData,'profit'));

        $count = count($xlsData)+2;
        $condition['row'] = 'A'.$count.':C'.$count;
        $condition['title'] = '合计';
        $condition['row1'] = 'D'.$count;
        $condition['row2'] = 'E'.$count;
        $condition['price'] = number_format($total['price'],2);;
        $condition['profit'] = number_format($total['profit'],2);

        /* 调用导出方法 */
        $this->exportExcel($xlsName, $xlsCell, $xlsData,$condition);
    }

    //销量转化率
    public function cvr()
    {
        $post = I('post.');
        if ($post['goodsname']) {
            $where['a.goodsname'] = array('like','%'.$post['goodsname'].'%');
            $this->assign('goodsname',$post['goodsname']);
        }

        $where['c.status'] = ['neq',2];
        $goodsModle = new GoodsModel();
        $count = count($goodsModle->getGoods($where,''));
        $page = $this->page($count,20);
        $data = $goodsModle->getGoods($where,$page);

        foreach ($data as &$v)
        {
            $v['cvr'] = number_format($v['cvr']*100,2);
        }

        $this->assign('data',$data);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    //导出销量转化率EXCEL
    public function export_cvr()
    {
        $get = I('get.');
        //数据库中的数据表
        $xlsName = "Stamp";
        $xlsCell = array(
            array('id', '序号'),
            array('goodsname', '商品名称'),
            array('clickcount', '访问次数'),
            array('number', '购买件数'),
            array('cvr', '转化率(%)'),
        );

        //昵称
        if ($get['goodsname']) {
            $where['a.goodsname'] = array('like',"%".$get['goodsname']."%");
        }

        $xlsModel = new GoodsModel();
        //导出所有的内容
        $where['c.status'] = ['neq',2];
        $xlsData = $xlsModel->getGoods($where,'');
        foreach ($xlsData as $k=>&$v)
        {
            $v['id'] = $k+1;
            $v['cvr'] = number_format($v['cvr']*100,2);
        }

        /* 调用导出方法 */
        $this->exportExcel($xlsName, $xlsCell, $xlsData);
    }

}