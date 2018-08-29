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
class PublicController extends BaseController {
	// 登录
	public function dologin()
	{
		$forget = I('post.');
    	if(empty($forget['mobile']) || empty($forget['password']))
    	{
    		$this->ajaxReturn(array('msg'=>'参数有误','status'=>'202','data'=>null));
    	}
    	else
    	{
    		$minfo =M('member')->field('id,password,nickname,mobile,avatar,coin,level_id,role_id,status')->where("mobile='".$forget['mobile']."'")->find();
    		if($minfo)
    		{
    			if($minfo['password'] != sp_password($forget['password']))
    			{
    				$this->ajaxReturn(array('msg'=>'密码错误','status'=>'204','data'=>null));
    			}else
    			{
					if($minfo['status'] == 0)
					{
						$this->ajaxReturn(array('msg'=>'该用户被冻结','status'=>'205','data'=>null));
					}
    				unset($minfo['password']);
    				$data['last_login_time']=date('Y-m-d H:i:s');
    				$data['last_login_ip'] =get_client_ip(0,true);
    				M('member')->where("id='".$minfo['id']."'")->save($data);
					if($minfo['role_id']>0)
					{
						$minfo['rolename'] =M('member_role')->where("id='".$minfo['role_id']."'")->getField('rolename');
					}
					if($minfo['level_id']>0)
					{
						$minfo['levelname'] =M('member_level')->where("id='".$minfo['level_id']."'")->getField('levelname');
					}
    				$this->ajaxReturn(array('msg'=>'登录成功','status'=>'200','data'=>$minfo));
    			}
    		}else
    		{
    			$this->ajaxReturn(array('msg'=>'手机号错误','status'=>'203','data'=>null));
    		}
    	}
	}
	// 注册
	public function doregister()
	{
		$forget = I('post.');
		if(empty($forget['mobile']) || empty($forget['password']) || empty($forget['confirmpassword']) || empty($forget['yzcode']) || empty($forget['getcode']))
		{
			$this->ajaxReturn(array('msg'=>'参数有误','status'=>'202','data'=>null));
		}
		if(!preg_match('/^1[3456789]{1}\d{9}$/',$forget['mobile']))
        {
            $this->ajaxReturn(array('status'=>'202','data'=>null,'msg'=>'手机号错误')); 
        }else
        {
        	$minfo =M('member')->where("mobile='".$forget['mobile']."'")->find();
        	if($minfo)
        	{
        		$this->ajaxReturn(array('status'=>'203','data'=>null,'msg'=>'手机号已注册'));
        	}else
        	{
        		if($forget['password'] != $forget['confirmpassword'])
        		{
        			$this->ajaxReturn(array('status'=>'204','data'=>null,'msg'=>'密码不一致'));
        		}
        		if($forget['yzcode'] != $forget['getcode'])
        		{
        			$this->ajaxReturn(array('status'=>'205','data'=>null,'msg'=>'验证码错误'));
        		}

        		$data['mobile']=$forget['mobile'];
        		$data['password'] =sp_password(trim($forget['password']));
        		$data['addtime'] =time();
        		$data['status']=1;
        		$data['role_id']=0;
        		$data['level_id'] =1;
        		$data['credit_time'] =0;
        		$res=M('member')->add($data);
        		if($res)
        		{
        			$dataa['id'] =$res;
        			$dataa['mobile'] =$data['mobile'];
        			$this->ajaxReturn(array('status'=>'200','data'=>$dataa,'msg'=>'注册成功'));
        		}else
        		{
        			$this->ajaxReturn(array('status'=>'206','data'=>null,'msg'=>'注册失败'));
        		}
        	}
        }		
	}
	// 忘记密码
	public function doforgetpwd()
	{
		$forget = I('post.');
		file_put_contents('a0009.txt',var_export($forget,true));
		if(empty($forget['mobile']) || empty($forget['password']) || empty($forget['confirmpassword']) || empty($forget['yzcode']) || empty($forget['getcode']))
		{
			$this->ajaxReturn(array('msg'=>'参数有误','status'=>'202','data'=>null));
		}
		
		$minfo =M('member')->where("mobile='".$forget['mobile']."'")->find();
		if($minfo)
		{
			if($forget['password'] != $forget['confirmpassword'])
    		{
    			$this->ajaxReturn(array('status'=>'204','data'=>null,'msg'=>'密码不一致'));
    		}
    		if($forget['yzcode'] != $forget['getcode'])
    		{
    			$this->ajaxReturn(array('status'=>'205','data'=>null,'msg'=>'验证码错误'));
    		}
    		$res=M('member')->where("mobile='".$forget['mobile']."'")->save(array('password'=>sp_password($forget['password'])));

    		if($res)
    		{
    			$this->ajaxReturn(array('msg'=>'修改成功','status'=>'200','data'=>$res));
    		}else if($minfo['password'] == sp_password($forget['password']))
    		{
    			$this->ajaxReturn(array('msg'=>'修改成功','status'=>'200','data'=>$minfo['id']));
    		}
    		else
    		{
    			$this->ajaxReturn(array('msg'=>'修改失败','status'=>'206','data'=>null));
    		}

		}else
		{
			$this->ajaxReturn(array('msg'=>'手机号错误','status'=>'202','data'=>null));
		}

	}
	// 获取短信验证码
	public function  getcode()
	{
		$forget = I('post.');
    	if(!preg_match('/^1[3456789]{1}\d{9}$/',$forget['mobile']))
        {
            $this->ajaxReturn(array('status'=>'202','data'=>null,'msg'=>'手机号错误')); 
        }
        else
        {
        	// 注册
	    	if($forget['dotype'] ==1)
	    	{
	    		$minfo =M('member')->where("mobile='".$forget['mobile']."'")->find();
	    		if(!$minfo)
	    		{
	    			$check=S('user_send'.$forget['mobile']);
		            if(!empty($check)&&$check>=5){
		                  $this->ajaxReturn(array('status'=>'203','data'=>null,'msg'=>'请勿频繁获取短信验证'));
		            }
		            $code=func_randStr(5);
	    			$content=sendSms($forget['mobile'],$code);
	    			if($content){
				        $result = json_decode($content,true);
				        $error_code = $result['error_code'];
				        if($error_code == 0){
				            //状态为0，说明短信发送成功
				            $data=array('code'=>$code);
			                $num=S('user_send'.$mobile) ? S('user_send'.$mobile):0;
			                S('user_send'.$mobile,++$num,3600);
				            $this->ajaxReturn(array('status'=>'200','msg'=>'短信发送成功','data'=>$data));
				        }
				    }else{
				       $this->ajaxReturn(array('status'=>'205','data'=>null,'msg'=>$result['reason']));
				    }	    			
	    		}else
	    		{
	    			$this->ajaxReturn(array('status'=>'202','data'=>null,'msg'=>'手机号已注册'));
	    		}
	    	}else // 找回密码
	    	{
	    		$minfo =M('member')->where("mobile='".$forget['mobile']."'")->find();
	    		if($minfo)
	    		{
	    			$check=S('user_send2'.$forget['mobile']);
		            if(!empty($check)&&$check>=5){
		                  $this->ajaxReturn(array('status'=>'203','data'=>null,'msg'=>'请勿频繁获取短信验证'));
		            }
		            $code=func_randStr(5);
	    			$content=sendSms($forget['mobile'],$code);
	    			if($content){
				        $result = json_decode($content,true);
				        $error_code = $result['error_code'];
				        if($error_code == 0){
				            //状态为0，说明短信发送成功
				            $data=array('code'=>$code);
			                $num=S('user_send2'.$mobile) ? S('user_send2'.$mobile):0;
			                S('user_send2'.$mobile,++$num,3600);
				            $this->ajaxReturn(array('status'=>'200','msg'=>'短信发送成功','data'=>$data));
				        }
				    }else{
				       $this->ajaxReturn(array('status'=>'205','data'=>null,'msg'=>$result['reason']));
				    }	    			
	    		}else
	    		{
	    			$this->ajaxReturn(array('status'=>'202','data'=>null,'msg'=>'手机号未注册'));
	    		}
	    	}
        }
	}

}