<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 
// +----------------------------------------------------------------------
// | Author: Dean <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
class IndexController extends HomebaseController {
	
    //首页
	public function index() {
    	$forget = I('get.');	
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	$type =$forget['catid'] ? $forget['catid'] :'1';
    	$list =M('banner')->field('id,title,post_img,url_link,termid,atype')->where("termid='$type' and status=1")->order('sortorder asc')->select();
    	foreach ($list as $key => $value) {
    		$list[$key]['catname'] =M('terms')->where("term_id='".$value['termid']."'")->getField('name');
    		if($value['post_img'])
    		{
    			$list[$key]['post_img'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$value['post_img'];
    		}
    	}
    	if(count($list) == 0)
    	{
    		$this->ajaxReturn2(array('state_code'=>200,'msg'=>'返回成功','data'=>null));
    	}else
    	{
    		$this->ajaxReturn2(array('state_code'=>200,'msg'=>'返回成功','data'=>$list));
    	}
    }
	 // 经典案例
    public function articleindex()
    {
    	$forget = I('get.');	
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	$type = intval($forget['catid']) ? $forget['catid'] :'1';
    	$page = intval($forget['page']) ? $forget['page'] :'1';

    	$count = M('posts')->alias("a")->join("__TERM_RELATIONSHIPS__ b On a.id = b.object_id")->where("b.term_id='$type'")->count();
    	$list = M('posts')->field('a.id,a.post_title,a.post_excerpt,a.agentcount,a.likecount,a.smeta')->alias("a")->join("__TERM_RELATIONSHIPS__ b On a.id = b.object_id")->where("b.term_id='$type'")->page($page.',10')->order('sort asc')->select();

    	$totalpage =ceil($count/10);

    	foreach ($list as $key => $value) {
    		if($value['smeta'])
    		{
    			$list[$key]['smeta'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$value['smeta'];
    		}
    	}

    	if(count($list) == 0)
    	{
    		$this->ajaxReturn2(array('state_code'=>200,'msg'=>'返回成功','data'=>null,'totalpage'=>$totalpage));
    	}else
    	{
    		$this->ajaxReturn2(array('state_code'=>200,'msg'=>'返回成功','data'=>$list,'totalpage'=>$totalpage));
    	}
    }
	 // 案例详情
    public function detail()
    {
    	
    	$forget = I('get.');	
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	$info =M('posts')->field('id,post_title,post_content,agentcount,likecount')->where("id='".intval($forget['id'])."'")->find();
    	if($info)
    	{
			$count=M('collect')->where("userid='".$forget['userid']."' and articleid='".$forget['id']."'")->count();
    		$info['isagent'] = $count > 0 ? 1 :0;  // 1 关注  0 未关注
			$address =M('member')->where("id='".$forget['userid']."'")->getField('address');
    		$info['address'] = $address;
    		$this->ajaxReturn2(array('state_code'=>200,'msg'=>'返回成功','data'=>$info));
    	}else
    	{
    		$this->ajaxReturn2(array('state_code'=>201,'msg'=>'参数有误','data'=>null));
    	}
    }
	// 关注
    public function agentarticle()
    {
    	$forget = I('get.');	
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	$isagent = intval($forget['isagent']);
    	if(empty($forget['userid']) || empty($forget['id']))
    	{
    		$this->ajaxReturn2(array('state_code'=>'201','msg'=>'参数有误','data'=>null));
    	}else
    	{
    		if($isagent == 0)
    		{
    			$result=M('collect')->add(array('userid'=>$forget['userid'],'articleid'=>$forget['id'],'addtime'=>time()));
	    		if($result)
	    		{
	    			M('posts')->where("id='".$forget['id']."'")->setInc('agentcount');
	    			$agentcount =M('posts')->where("id='".$forget['id']."'")->getField('agentcount');
	    			$this->ajaxReturn2(array('state_code'=>'200','msg'=>'关注成功','data'=>$info,'agentcount'=>$agentcount));
	    		}
    		}else
    		{
    			M('posts')->where("id='".$forget['id']."'")->setDec('agentcount',1);
    			M('collect')->where("userid='".$forget['userid']."' and articleid='".$forget['id']."'")->delete();
    			$agentcount =M('posts')->where("id='".$forget['id']."'")->getField('agentcount');
    			$this->ajaxReturn2(array('state_code'=>'200','msg'=>'取消关注成功','data'=>$info,'agentcount'=>$agentcount));
    		}    		
    	}
    }
	
	// 点赞
    public function  addlike()
    {
    	$forget = I('get.');	
    	if($forget['token'] != C('GETTOKRN'))
    	{
    		$this->ajaxReturn2(array('msg'=>'token验证失败','state_code'=>'201','data'=>null));
    	}
    	$info =M('posts')->where("id='".$forget['id']."'")->find();
    	if($info)
    	{
    		M('posts')->where("id='".$forget['id']."'")->setInc('likecount');
    		$likecount =M('posts')->where("id='".$forget['id']."'")->getField('likecount');
    		$this->ajaxReturn2(array('state_code'=>'200','msg'=>'点赞成功','data'=>$likecount));
    	}else
    	{
    		$this->ajaxReturn2(array('state_code'=>'203','msg'=>'参数有误','data'=>null));
    	}
    	
    }
}


