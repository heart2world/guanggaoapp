<?php
// +----------------------------------------------------------------------
// | ThinkCMF 客户管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MemberController extends AdminbaseController {
	// 会员管理列表
	public function index(){				
		$where="1=1";	
		$this->member =M('member');
		$keyword=I('keyword','','trim');
		
		if(!empty($keyword)){
		    $where .= " and nickname like '%$keyword%' or mobile like '%$keyword%'";
		}
		
		$count=$this->member->where($where)->count();			
		$page = $this->page($count, 20);			
		$member=$this->member
				->where($where)
				->limit($page->firstRow , $page->listRows)
				->order('addtime desc')
				->select();
		foreach ($member as $key => $value) {
            $member[$key]['rolename'] =M('member_role')->where("id='".$value['role_id']."'")->getField('rolename');
            $member[$key]['levelname'] =M('member_level')->where("id='".$value['level_id']."'")->getField('levelname');

            $timestr=$value['credit_day'] - ((strtotime(date("Y-m-d"),time()) - strtotime(date("Y-m-d",$value['credit_time'])))/(60*60*24));

			if($timestr > 0)
			{
				$member[$key]['credit_day'] =$timestr;
			}else{
				$member[$key]['credit_day'] =0;
			}			
        }
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("member",$member);

        $level =M('member_level')->select();
        $this->assign("level",$level);
        $roles =M('member_role')->select();
        $this->assign("roles",$roles);
		$this->display();
	}
    // 会员角色管理
    public function roleindex(){                
      
        $this->member =M('member_role');        
        $count=$this->member->count();           
        $page = $this->page($count, 20);            
        $role=$this->member              
                ->limit($page->firstRow , $page->listRows)
                ->select();        
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("role",$role);
        $this->display();
    }

    // 会员等级管理
    public function level(){                
      
        $this->member =M('member_level');        
        $count=$this->member->count();           
        $page = $this->page($count, 20);            
        $level=$this->member              
                ->limit($page->firstRow , $page->listRows)
                ->select();        
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("level",$level);
        $this->display();
    }

    // 会员等级说明
    public function leveldec()
    {
        $option_value = M('options')->where("option_name='level_options'")->getField('option_value');

        $info =json_decode($option_value,true);
        $this->assign('content',$info['content']);
        $this->display();
    }
    public function addcontent_post()
    {
        if(IS_POST)
        {
            $content =I('post.post_content');
            if(empty($content))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入完整信息'));
            }
            $options['content'] = htmlspecialchars_decode($content);
            $data['option_value']=json_encode($options);
            M('options')->where("option_name='level_options'")->save($data);
            $this->ajaxReturn(array('status'=>0,'msg'=>'保存成功'));
        }
    }
    // 添加等级
    public function addlevel()
    {
        $this->display();
    }
    public function addlevel_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['levelname']) || empty($pdata['leveldesc']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入完整信息'));
            }
            if($pdata['maxprice']<0)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入正确的满额数'));
            }
            if($pdata['persont']<0 || $pdata['persont'] > 10)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入0-10之间的折扣比例'));
            }
            $pdata['maxprice'] =round($pdata['maxprice'],2);
            $pdata['persont'] =round($pdata['persont'],2);
            $pdata['create_time']=time();
            $lastid=M('member_level')->add($pdata);
            if($lastid)
            {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存成功'));
            }else
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存失败'));
            }
        }
    }
    public function editlevel()
    {
        $id = I("get.id",0,'intval');

        $info =M('member_level')->find($id);
        $this->assign('info',$info);
        $this->display();
    }
    public function editlevel_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['levelname']) || empty($pdata['leveldesc']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入完整信息'));
            }
            if($pdata['maxprice']<0)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入正确的满额数'));
            }
            if($pdata['persont']<0 || $pdata['persont'] > 10)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入0-10之间的折扣比例'));
            }
            $pdata['maxprice'] =round($pdata['maxprice'],2);
            $pdata['persont'] =round($pdata['persont'],2);
            
            $lastid=M('member_level')->save($pdata);
           
            $this->ajaxReturn(array('status'=>0,'msg'=>'保存成功'));
           
        }
    }
    // 添加角色
    public function addrole()
    {
        $this->display();
    }
     // 添加保存
    public function addrole_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['rolename']) || empty($pdata['roledesc']) || $pdata['persont']==0)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入完整信息'));
            }
            if($pdata['persont']<0 || $pdata['persont'] > 100)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入0-100之间的价格比例'));
            }
            $pdata['persont'] =round($pdata['persont'],2);
            $pdata['create_time']=time();
            $lastid=M('member_role')->add($pdata);
            if($lastid)
            {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存成功'));
            }else
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存失败'));
            }
        }
    }
    // 编辑
    public function editrole()
    {
        $id = I("get.id",0,'intval');

        $info =M('member_role')->find($id);
        $this->assign('info',$info);
        $this->display();
    }
    public function editrole_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['rolename']) || empty($pdata['roledesc']) || $pdata['persont']==0)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入完整信息'));
            }
            if($pdata['persont']<0 || $pdata['persont'] > 100)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入0-100之间的价格比例'));
            }  
            $pdata['persont'] =round($pdata['persont'],2);         
            $lastid=M('member_role')->save($pdata);           
            $this->ajaxReturn(array('status'=>0,'msg'=>'保存成功'));            
        }
    }
    // 添加会员
    public function add()
    {
        $roleids =M('member_role')->order('create_time desc')->select();
        $this->assign('roleids',$roleids);
        $levelids =M('member_level')->order('create_time desc')->select();
        $this->assign('levelids',$levelids);
        $this->display();
    }
    // 添加保存
    public function add_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['mobile']) || $pdata['role_id'] == 0 || $pdata['level_id'] ==0 || empty($pdata['user_pass']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入完整信息'));
            }
            if(!preg_match('/^1[345789]{1}\d{9}$/', $pdata['mobile']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请录入正确的手机号'));
            }
            $pdata['addtime']=time();
            $pdata['credit_time']=time();
			$pdata['password'] =sp_password($pdata['user_pass']);
            $lastid=M('member')->add($pdata);
            if($lastid)
            {
                $this->ajaxReturn(array('status'=>0,'msg'=>'保存成功'));
            }else
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'保存失败'));
            }
        }
    }
    // 冻结会员	
    public function ban(){
        $id = I('get.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('member')->where(array("id"=>$id))->setField('status','0');
    		if ($result!==false) {
    			$this->success("冻结成功！", U("member/index"));
    		} else {
    			$this->error('冻结失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    // 恢复会员 
    public function cancelban(){
    	$id = I('get.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('member')->where(array("id"=>$id))->setField('status','1');
    		if ($result!==false) {
    			$this->success("恢复成功！", U("member/index"));
    		} else {
    			$this->error('恢复失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
	public function deleterole()
    {
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if (M('member_role')->where(array('id'=>$id))->delete() !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }
    public function deletelevel()
    {
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if (M('member_level')->where(array('id'=>$id))->delete() !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }
    // 更改角色
    public function changerole()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            
            $res=M('member')->where("id='".$pdata['uid']."'")->setField('role_id',$pdata['role_id']);
            $info =M('member_role')->where("id='".$pdata['role_id']."'")->find();
           
           
            $this->ajaxReturn(array('status'=>0,'msg'=>'修改成功','rolename'=>$info['rolename'],'uid'=>$pdata['uid']));
        }
    }
	// 更改赊账天数
	public function changecreditday()
	{
	    if(IS_POST)
        {
            $pdata =I('post.');
            $pdata['credit_time']=time();
			if(!preg_match('/^[0-9]*$/',$pdata['credit_day']))
			{
				$this->ajaxReturn(array('status'=>1,'msg'=>'赊账天数只能是数字'));
			}
            $res=M('member')->where("id='".$pdata['uid']."'")->save($pdata);            
            $this->ajaxReturn(array('status'=>0,'msg'=>'修改成功'));
        }
	}
    // 更改角色
    public function changelevel()
    {
        if(IS_POST)
        {
            $pdata =I('post.');

            $res=M('member')->where("id='".$pdata['uid']."'")->setField('level_id',$pdata['level_id']);
            $rolename =M('member_level')->where("id='".$pdata['level_id']."'")->getField('levelname');
            $this->ajaxReturn(array('status'=>0,'msg'=>'修改成功','levelname'=>$rolename,'uid'=>$pdata['uid']));
           
        }
    }
    //更改备注
    public function changeremark()
    {
        if(IS_POST)
        {
            $pdata =I('post.');

            $res=M('member')->where(['id'=>$pdata['uid']])->save(['remark1'=>$pdata['remark1']]);

            if ($res) {
                $this->ajaxReturn(array('status'=>0,'msg'=>'修改成功','remark1'=>$pdata['remark1'],'uid'=>$pdata['uid']));
            }else{
                $this->ajaxReturn(array('status'=>1,'msg'=>'修改失败','remark1'=>$pdata['remark1'],'uid'=>$pdata['uid']));
            }
        }
    }
}