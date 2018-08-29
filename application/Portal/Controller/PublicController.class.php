<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
/**
 * 首页
 */
class PublicController extends HomebaseController {
	
    //首页 小夏是老猫除外最帅的男人了
	public function login() {
		$forget = I('post.');	
		file_put_contents('a.txt',var_export($forget,true));
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	if(empty($forget['mobile']) || empty($forget['password']))
    	{
    		$this->ajaxReturn2(array('msg'=>'账户或密码不能为空','state_code'=>'202','data'=>null));
    	}else
    	{
    		$minfo =M('member')->field('id,mobile,password,nicename,duty,address,email,avatar,weixincode,status')->where("mobile='".$forget['mobile']."'")->find();
    		if(empty($minfo))
    		{
    			$this->ajaxReturn2(array('msg'=>'暂无该账号','state_code'=>'203','data'=>null));
    		}else
    		{
    			if($minfo['password'] != sp_password(trim($forget['password'])))
    			{
    				$this->ajaxReturn2(array('msg'=>'密码错误请重新输入','state_code'=>'204','data'=>null));
    			}else
    			{
					unset($minfo['password']);
					if($minfo['avatar'])
					{
						$minfo['avatar'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$minfo['avatar'];
					}
					if($minfo['weixincode'])
					{
						$minfo['weixincode'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$minfo['weixincode'];
					}
    				$this->ajaxReturn2(array('state_code'=>200,'msg'=>'返回成功','data'=>$minfo));
    			}
    		}
    	}
    	
    }
	// 验证账户是否冻结
    public function yzmember()
    {
    	$forget = I('get.');		
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	
		$minfo =M('member')->field('id,status')->where("id='".trim($forget['userid'])."'")->find();
		if($minfo)
		{
			$this->ajaxReturn2(array('state_code'=>'200','msg'=>'返回成功','data'=>$minfo));
		}else
		{
			$this->ajaxReturn2(array('state_code'=>'202','msg'=>'参数有误','data'=>null));
		}
    }

}


