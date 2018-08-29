<?php
// +----------------------------------------------------------------------
// | ThinkCMF 会员管理中心板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
class CenterController extends BaseController {
	// 获取基本会员资料
	public function index()
	{
		$forget = I('get.');   	
		$minfo =M('member')->field('id,nickname,mobile,avatar,coin,level_id,role_id')->where("id='".$forget['uid']."'")->find();
		if($minfo)
		{
			if(empty($minfo['avatar']))
            {
                $minfo['avatar'] = 'http://'.$_SERVER['HTTP_HOST'].'/data/upload/logo.png';
            }else
            {
                $minfo['avatar'] = $minfo['avatar'];
            }
            if($minfo['role_id']>0)
            {
                $minfo['rolename'] =M('member_role')->where("id='".$minfo['role_id']."'")->getField('rolename');
            }
            if($minfo['level_id']>0)
            {
                $minfo['levelname'] =M('member_level')->where("id='".$minfo['level_id']."'")->getField('levelname');
            }
			$this->ajaxReturn(array('msg'=>'返回成功','status'=>'200','data'=>$minfo));
			
		}else
		{
			$this->ajaxReturn(array('status'=>'203','data'=>null,'msg'=>'该会员尚未注册或参数有误'));
		}    	
	}
	// 修改个人信息
	public function editinfo()
	{
		$forget =I('post.');
		if(empty($forget['nickname']) || empty($forget['uid']) || empty($forget['avatar']))
		{
			$this->ajaxReturn(array('msg'=>'参数有误','status'=>'201','data'=>null));
		}
		file_put_contents('a45.txt',var_export($forget,true));
		$result=M('member')->where("id='".$forget['uid']."'")->save(array('nickname'=>$forget['nickname'],'avatar'=>$forget['avatar']));
		$this->ajaxReturn(array('msg'=>'保存成功','status'=>'200','data'=>$result));
	}
	
	/**
    * [uploadOneimg 上传单张图片接口]
    * @return [type] [description]
    */
    public function uploadOneimg()
    {
        
        $forget =I('post.');
        $info =M('member')->where("id='".$forget['uid']."'")->find();
        if(!empty($info))
        {                
            if($_FILES['file']['name']!='')
            {
               $date['imgpath'] = uploadOne($_FILES['file'],"images");  
            
               if($date['imgpath'])
               {
                 $imgpath = makeoldImg2($date['imgpath'],'200','200');
               
                 $date['imgpath'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$imgpath;      
               }    
				
               $this->ajaxReturn(array('status'=>'200','data'=>array('imgpath'=> $date['imgpath']),'msg'=>'上传成功'));
            }
            else
            {
               $this->ajaxReturn(array('status'=>'202','data'=>null,'msg'=>'参数命名有误'));
            }
        }else
        {
            $this->ajaxReturn(array('status'=>'203','data'=>null,'msg'=>'该会员尚未注册或参数有误'));
        }          
    }
    // 收藏商品列表接口
    public function getcollectgoods()
    {
    	$forget =I('get.');
        $info =M('member')->where("id='".$forget['uid']."'")->find();
        if(!empty($info))
        { 
            $page =$forget['page']?$forget['page']:'1';
            $count = M('collect')->where("userid='".$forget['uid']."'")->count();
        	$list=M('collect')->where("userid='".$forget['uid']."'")->page($page.',10')->order('createtime desc')->select();
        	
			$totalpage = ceil($count/10);
            if(count($list) ==0)
        	{
        		$this->ajaxReturn(array('status'=>'200','data'=>null,'msg'=>'返回成功'));
        	}else
        	{
				$liststr =array();
        		foreach ($list as $key => $value) {
					$goods =M('goods')->field('goodsname,thumb_img,price')->find($value['goods_id']);
        			if($goods['thumb_img'])
        			{
        				$liststr[$key]['thumb_img'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$goods['thumb_img'];
        			}
					$liststr[$key]['goodsname']=$goods['goodsname'];
					$liststr[$key]['goods_id']=$value['goods_id'];
					$liststr[$key]['price']=$goods['price'];
					$liststr[$key]['id']=$value['id'];
        		}
				
        		$this->ajaxReturn(array('status'=>'200','data'=>$liststr,'msg'=>'返回成功','totalpage'=>$totalpage));
        	}
        	
        }else
        {
            $this->ajaxReturn(array('status'=>'203','data'=>null,'msg'=>'该会员尚未注册或参数有误'));
        } 
    }
    // 删除收藏
    public function delcollect()
    {
    	$forget =I('get.');
        $info =M('member')->where("id='".$forget['uid']."'")->find();
        if(!empty($info))
        { 
        	if(!empty($forget['idstr']))
        	{
        		$res=M('collect')->where("userid='".$forget['uid']."' and id in (".$forget['idstr'].")")->delete();
        		$this->ajaxReturn(array('status'=>'200','data'=>null,'msg'=>'删除成功'));
        	}else
        	{
        		$this->ajaxReturn(array('status'=>'202','data'=>null,'msg'=>'请选择删除商品'));
        	}
        }else
        {
            $this->ajaxReturn(array('status'=>'203','data'=>null,'msg'=>'该会员尚未注册或参数有误'));
        } 
    }






























}