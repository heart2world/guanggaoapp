<?php
// +----------------------------------------------------------------------
// | ThinkCMF 公共管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
	// 微信预生成prepay_id
	public function wxpay()
	{
	    $pdata =I('post.');
	    Vendor("WxPayPubHelper.WxPayPubHelper"); 
        if(!empty($pdata['order_num']) && !empty($pdata['order_total']))
        {
            $orderBody = "嘉鑫辉广告材料";
            $tade_no = $pdata['order_num'] . time();
            $WxPayHelper = new \WxPayHelper();
            $response = $WxPayHelper->getPrePayOrder($orderBody, $tade_no, $pdata['order_total']);  
            //file_put_contents('a3_2.txt',var_export($response,true));
            $x = $WxPayHelper->getOrder($response['prepay_id']);
            $this->ajaxReturn(array('status'=> '200','data'=> $x,'msg'=>'返回成功'));
    	}else
    	{
    	  	$this->ajaxReturn(array('status'=> '203','data'=>null,'msg'=>'参数有误'));
    	}
	}
	// 微信回调地址
	public function jsApiCall()
    {
		$postXML =file_get_contents('php://input');
        $postObj = simplexml_load_string($postXML, 'SimpleXMLElement', LIBXML_NOCDATA);  
        //file_put_contents('pay1.txt', var_export($postObj,true));     
        if($postObj->return_code =='SUCCESS')
        {
       
            $order_num = $postObj->out_trade_no;//获取订单号
            $order_num = (String)$order_num;//获取订单号            
            $order_num = substr($order_num,0,16);
            $payinfo =M('pay_log')->where("ordersn='$order_num'")->find();
            if(!empty($payinfo) && $payinfo['ispaid']==0)
            {
                // 以下为改变订单状态
                M('pay_log')->where("ordersn='$order_num'")->setField('ispaid',1);
                // 订单
                if($payinfo['ordertype'] ==1)
                {
                    M('order')->where("orderno='$order_num'")->save(array('pay_time'=>time(),'status'=>3,'paytype'=>1));
                }else // 充值
                {
                    M('recharge')->where("recharge_no='$order_num'")->save(array('paytime'=>date('Y-m-d H:i:s'),'status'=>1));
                }                
            }else
            {
                echo 'fail';
            }            
        }else
        {
            echo 'fail';
        }
    }

    // 支付宝回调
    public function aliNotify()
    {
        //订单号
        $out_trade_no = $_POST['out_trade_no'];
        //支付宝交易号
        $trade_no = $_POST['trade_no'];
        //订单金额
        $total_fee = $_POST['total_fee'];
        //交易状态
        $trade_status = $_POST['trade_status'];
        file_put_contents('pay_2.txt', var_export($_POST,true)); 
        $body = $_POST['body'];
        $myfile = fopen("alipay.txt", "w") or die("Unable to open file!");
        $txt = $out_trade_no."|||".$trade_no."|||".$total_fee."|||".$trade_status."|||".$body;
        fwrite($myfile, $txt);
        fclose($myfile);
        if($trade_status == 'TRADE_SUCCESS') {
            $order_num = (String)$out_trade_no; //获取订单号
            $payinfo =M('pay_log')->where("ordersn='$order_num'")->find();
            if(!empty($payinfo) && $payinfo['ispaid']==0)
            {
                // 以下为改变订单状态
                M('pay_log')->where("ordersn='$order_num'")->setField('ispaid',1);
                // 订单
                if($payinfo['ordertype'] ==1)
                {
                    M('order')->where("orderno='$order_num'")->save(array('pay_time'=>time(),'status'=>3,'paytype'=>2));
                }else // 充值
                {
                    M('recharge')->where("recharge_no='$order_num'")->save(array('paytime'=>date('Y-m-d H:i:s'),'status'=>1));
                }                
            }else
            {
                echo 'fail';
            }        
        }else
        {
            echo 'fail';
        }
    }
}