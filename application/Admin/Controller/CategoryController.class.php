<?php
// +----------------------------------------------------------------------
// | ThinkCMF 商品分类表
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CategoryController extends AdminbaseController {
	// 后台客户管理列表
	public function index(){				
		$where=array();	
		$this->catemodel =M('category');
		$keyword=I('keyword','','trim');		

		if(!empty($keyword)){
		    $where['catname']=array('like',"%$keyword%");
		}
		
		$count=$this->catemodel->where($where)->count();			
		$page = $this->page($count, 20);			
		$list=$this->catemodel
				->where($where)
				->limit($page->firstRow , $page->listRows)
				->order('sortorder ASC')
				->select();
        foreach ($list as $key => $value) {
           if($value['catimg'])
           {
                $list[$key]['catimg2'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$value['catimg'];
           }
        }
       
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("list",$list);
		$this->display();
	}	
    public function add()
    {
        $this->display();
    }
    public function add_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['catname']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请输入分类名称'));
            }
            if(empty($pdata['catimg']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请上传分类图标'));
            }
            $count =M('category')->where("catname='".$pdata['catname']."'")->count();
            if($count > 0)
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'分类名称已经存在'));
            }
            $pdata['createtime']=time();
           
            $result= M('category')->add($pdata);
            if($result)
            {
                $this->ajaxReturn(array('status'=>0,'msg'=>'添加成功'));            
            }else
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'添加失败')); 
            }
        }
    }
    public function edit()
    {
        $id =I('id','','intval');
        if($id)
        {
            $member =M('category')->find($id);
            if($member['catimg'])
            {
                $member['catimg2'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$member['catimg'];
            }
            $this->assign('info',$member);
            $this->display();
        }
    }
    
    public function edit_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['catname']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请输入分类名称'));
            }
            if(empty($pdata['catimg']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请上传分类图标'));
            }
            if($pdata['catname'] != $pdata['oldcatname'])
            {
                $count =M('category')->where("catname='".$pdata['catname']."'")->count();
                if($count > 0)
                {
                    $this->ajaxReturn(array('status'=>1,'msg'=>'分类名称已经存在'));
                }
            }
            
            $result= M('category')->where("id='".$pdata['id']."'")->save($pdata);
           
            $this->ajaxReturn(array('status'=>0,'msg'=>'编辑成功'));            
            
        }
    }
    public function delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if (M('category')->where(array('id'=>$id))->delete() !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        
    }
    public function ban(){
        $id = I('get.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('category')->where(array("id"=>$id))->setField('status','0');
    		if ($result!==false) {
    			$this->success("下架成功！", U("category/index"));
    		} else {
    			$this->error('下架失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    public function cancelban(){
    	$id = I('get.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('category')->where(array("id"=>$id))->setField('status','1');
    		if ($result!==false) {
    			$this->success("上架成功！", U("category/index"));
    		} else {
    			$this->error('上架失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
	
}