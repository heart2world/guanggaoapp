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
class CenterController extends HomebaseController {
	
    //首页 小夏是老猫除外最帅的男人了
	public function index() {
		$forget = I('get.');		
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	
		$minfo =M('member')->field('id,mobile,nicename,duty,address,email,avatar,weixincode,status')->where("id='".trim($forget['userid'])."'")->find();
		if(empty($minfo))
		{
			$this->ajaxReturn2(array('msg'=>'参数有误','state_code'=>'203','data'=>null));
		}else
		{
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
	// 修改名称接口
    public function editname()
    {
        $forget = I('post.');        
        if($forget['token'] != C('GETTOKRN'))
        {
            $this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
        }
        if(empty($forget['nicename']) || empty($forget['userid']))
        {
            $this->ajaxReturn2(array('msg'=>'参数有误','state_code'=>'202','data'=>null));
        }else
        {
           $result= M('member')->where("id='".$forget['userid']."'")->save(array('nicename'=>trim($forget['nicename'])));          
           $this->ajaxReturn2(array('state_code'=>'200','msg'=>'保存成功','data'=>$result));  
        }
    }
    // 修改名称接口
    public function editmobile()
    {
        $forget = I('post.');        
        if($forget['token'] != C('GETTOKRN'))
        {
            $this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
        }
        if(empty($forget['mobile']) || empty($forget['userid']))
        {
            $this->ajaxReturn2(array('msg'=>'参数有误','state_code'=>'202','data'=>null));
        }else
        {
           $oldmobile= M('member')->where("id='".$forget['userid']."'")->getField('mobile');
           if($oldmobile != $forget['mobile'])
           {
                $count =M('member')->where("mobile='".$forget['mobile']."'")->count();
                if($count>0)
                {
                    $this->ajaxReturn2(array('msg'=>'该手机号已经注册','state_code'=>'202','data'=>null));
                }else
                {
                    $result= M('member')->where("id='".$forget['userid']."'")->save(array('mobile'=>trim($forget['mobile'])));
                    $this->ajaxReturn2(array('state_code'=>'200','msg'=>'保存成功','data'=>$result));   
                }
                
           }else
           {
               $this->ajaxReturn2(array('state_code'=>'200','msg'=>'保存成功','data'=>$result));   
           }
        }
    }
    // 修改邮箱
    public function editemail()
    {
        $forget = I('post.');        
        if($forget['token'] != C('GETTOKRN'))
        {
            $this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
        }
        if(empty($forget['email']) || empty($forget['userid']))
        {
            $this->ajaxReturn2(array('msg'=>'参数有误','state_code'=>'202','data'=>null));
        }else
        {
            $result= M('member')->where("id='".$forget['userid']."'")->save(array('email'=>trim($forget['email'])));
            $this->ajaxReturn2(array('state_code'=>'200','msg'=>'保存成功','data'=>$result));
        }
    }
    // 修改公司地址
    public function editaddress()
    {
        $forget = I('post.');        
        if($forget['token'] != C('GETTOKRN'))
        {
            $this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
        }
        if(empty($forget['address']) || empty($forget['userid']))
        {
            $this->ajaxReturn2(array('msg'=>'参数有误','state_code'=>'202','data'=>null));
        }else
        {
            $result= M('member')->where("id='".$forget['userid']."'")->save(array('address'=>intval($forget['address'])));
            $this->ajaxReturn2(array('state_code'=>'200','msg'=>'保存成功','data'=>$result));
        }
    }
    // 修改职务
    public function editduty()
    {
        $forget = I('post.');        
        if($forget['token'] != C('GETTOKRN'))
        {
            $this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
        }
        if(empty($forget['duty']) || empty($forget['userid']))
        {
            $this->ajaxReturn2(array('msg'=>'参数有误','state_code'=>'202','data'=>null));
        }else
        {
            $result= M('member')->where("id='".$forget['userid']."'")->save(array('duty'=>$forget['duty']));
            $this->ajaxReturn2(array('state_code'=>'200','msg'=>'保存成功','data'=>$result));
        }
    }
    // 上传图片接口
    public function editavatar()
    {
        $forget = I('post.');        
        if($forget['token'] != C('GETTOKRN'))
        {
            $this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
        }
        $info =M('member')->field('id,mobile')->where("id='".$forget['userid']."'")->find();
        if(!empty($info))
        {
			
            if($_FILES['file']['name'] !='')
            {
				if($forget['atype'] == 1)
				{
					$date['imgpath'] = uploadOne($_FILES['file'],"portal");  
				    $result =M('member')->where("id='".$forget['userid']."'")->save(array('avatar'=>$date['imgpath']));  
				    $date['imgpath'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$date['imgpath'];          
				}else
				{
				    $date['imgpath'] = uploadOne($_FILES['file'],"portal");  
					$result =M('member')->where("id='".$forget['userid']."'")->save(array('weixincode'=>$date['imgpath']));  
					$date['imgpath'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$date['imgpath'];          
				}	
               $this->ajaxReturn2(array('state_code'=>'200','data'=>array('imgpath'=> $date['imgpath']),'msg'=>'返回成功'));
            }
            else
            {
               $this->ajaxReturn2(array('state_code'=>'203','data'=>null,'msg'=>'参数命名有误'));
            }

        }else
        {
            $this->ajaxReturn2(array('state_code'=>'202','data'=>null,'msg'=>'参数有误'));
        }
    }
	 // 我的关注
    public function collect()
    {
        $forget = I('get.');        
        if($forget['token'] != C('GETTOKRN'))
        {
            $this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
        }

        $list=M('posts')->field('a.id,a.post_title,a.smeta,a.agentcount,a.likecount,a.post_excerpt')->alias("a")->join("__COLLECT__ c On c.articleid=a.id")->where("c.userid='".$forget['userid']."'")->select();
        if(count($list) == 0)
        {
            $this->ajaxReturn2(array('state_code'=>'200','data'=>null,'msg'=>'正常返回'));
        }else
        {
            foreach ($list as $key => $value) {
                if($value['smeta'])
                {
                    $list[$key]['smeta'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$value['smeta'];
                }
            }
            $this->ajaxReturn2(array('state_code'=>'200','data'=>$list,'msg'=>'正常返回'));
        }
    }

}


