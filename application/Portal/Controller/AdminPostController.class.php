<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class AdminPostController extends AdminbaseController {
    
	protected $posts_model;
	protected $term_relationships_model;
	protected $terms_model;
	
	function _initialize() {
		parent::_initialize();
		$this->posts_model = D("Portal/Posts");
		$this->terms_model = D("Portal/Terms");
		$this->term_relationships_model = D("Portal/TermRelationships");
	}
	
	// 后台文章管理列表
	public function index(){
		$this->_lists(array("post_status"=>array('neq',3)));
		$this->_getTree();
		$this->display();
	}
	
	// 文章添加
	public function add(){
		$terms = $this->terms_model->order(array("listorder"=>"asc"))->select();
		$term_id = I("get.term",0,'intval');
		$this->_getTermTree();
		$term=$this->terms_model->where(array('term_id'=>$term_id))->find();
		$this->assign("term",$term);
		$this->assign("terms",$terms);
		$this->display();
	}
	
	// 文章添加提交
	public function add_post(){
		if (IS_POST) {
			$article=I("post.post");
			if(empty($article['term'])){
				$this->ajaxReturn(array('msg'=>"请选择分类！",'status'=>1));
			}			
			if(empty($article['post_title'])){
				$this->ajaxReturn(array('msg'=>"请填写标题！",'status'=>1));
			}
			if(empty($article['post_excerpt'])){
				$this->ajaxReturn(array('msg'=>"请填写案例描述！",'status'=>1));
			}
			if(empty($article['post_content'])){
				$this->ajaxReturn(array('msg'=>"请填写案例内容！",'status'=>1));
			}
			if(empty($_POST['smeta']['thumb'])){
				$this->ajaxReturn(array('msg'=>"请上传缩略图！",'status'=>1));
			}
			$article=I("post.post");
			$article['smeta'] = trim($_POST['smeta']['thumb']);
			$article['post_modified']= date("Y-m-d H:i:s",time());
			$article['post_content']=htmlspecialchars_decode($article['post_content']);
			$article['post_author'] =get_current_admin_id();
			$result=$this->posts_model->add($article);			
			if ($result) {				
					$this->term_relationships_model->add(array("term_id"=>intval($article['term']),"object_id"=>$result));								
				$this->ajaxReturn(array('msg'=>"添加成功！",'status'=>0));
			} else {
				$this->ajaxReturn(array('msg'=>"添加失败！",'status'=>1));
			}
			 
		}
	}
	
	// 文章编辑
	public function edit(){
		$id=  I("get.id",0,'intval');
		
		$term_relationship = M('TermRelationships')->where(array("object_id"=>$id,"status"=>1))->getField("term_id");
		
		$this->_getTermTree($term_relationship);
		$terms=$this->terms_model->select();
		$post=$this->posts_model->where("id=$id")->find();
		if($post['smeta'])
		{
			$post['smeta2'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$post['smeta'];
		}

		$this->assign("post",$post);
		$this->assign("smeta",json_decode($post['smeta'],true));
		$this->assign("terms",$terms);
		$this->assign("term",$term_relationship);
		$this->display();
	}
	
	// 文章编辑提交
	public function edit_post(){
		if (IS_POST) {
			$article=I("post.post");
			if(empty($article['term'])){
				$this->ajaxReturn(array('msg'=>"请选择分类！",'status'=>1));
			}			
			if(empty($article['post_title'])){
				$this->ajaxReturn(array('msg'=>"请填写标题！",'status'=>1));
			}
			if(empty($article['post_excerpt'])){
				$this->ajaxReturn(array('msg'=>"请填写案例描述！",'status'=>1));
			}
			if(empty($article['post_content'])){
				$this->ajaxReturn(array('msg'=>"请填写案例内容！",'status'=>1));
			}
			if(empty($_POST['smeta']['thumb'])){
				$this->ajaxReturn(array('msg'=>"请上传缩略图！",'status'=>1));
			}
			$article=I("post.post");
			$article['smeta'] = trim($_POST['smeta']['thumb']);
			$article['post_modified']= date("Y-m-d H:i:s",time());
			$article['post_content']=htmlspecialchars_decode($article['post_content']);
			$post_id=intval($article['id']);
			
			$this->term_relationships_model->where(array("object_id"=>$post_id,"term_id"=>array("not in",$article['term'])))->delete();
			
				$find_term_relationship=$this->term_relationships_model->where(array("object_id"=>$post_id,"term_id"=>$article['term']))->count();
				if(empty($find_term_relationship)){
					$this->term_relationships_model->add(array("term_id"=>intval($article['term']),"object_id"=>$post_id));
				}else{
					$this->term_relationships_model->where(array("object_id"=>$post_id,"term_id"=>$article['term']))->save(array("status"=>1));
				}
			
			$result=$this->posts_model->where("id='".$post_id."'")->save($article);
			file_put_contents('a09.txt', $this->posts_model->getLastSql());
			$this->ajaxReturn(array('msg'=>"编辑成功",'status'=>0));
		}
	}
	
	// 文章排序
	public function listorders() {
		$status = parent::_listorders($this->term_relationships_model);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
	
	/**
	 * 文章列表处理方法,根据不同条件显示不同的列表
	 * @param array $where 查询条件
	 */
	private function _lists($where=array()){
		$term_id=I('request.term',0,'intval');
		
		$where['post_type']=array(array('eq',1),array('exp','IS NULL'),'OR');
		
		if(!empty($term_id)){
		    $where['b.term_id']=$term_id;
			$term=$this->terms_model->where(array('term_id'=>$term_id))->find();
			$this->assign("term",$term);
		}
		
		$start_time=I('request.start_time');
		if(!empty($start_time)){
		    $where['post_date']=array(
		        array('EGT',$start_time)
		    );
		}
		
		$end_time=I('request.end_time');
		if(!empty($end_time)){
		    if(empty($where['post_date'])){
		        $where['post_date']=array();
		    }
		    array_push($where['post_date'], array('ELT',$end_time));
		}
		
		$keyword=I('request.keyword');
		if(!empty($keyword)){
		    $where['post_title']=array('like',"%$keyword%");
		}
			
		$this->posts_model
		->alias("a")
		->where($where);
		
		if(!empty($term_id)){
		    $this->posts_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
		}
		
		$count=$this->posts_model->count();
			
		$page = $this->page($count, 10);
			
		$this->posts_model
		->alias("a")
		->join("__USERS__ c ON a.post_author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("a.sort ASC,a.post_date DESC");
		if(empty($term_id)){
		    $this->posts_model->field('a.*,c.user_login,c.user_nicename');
		}else{
		    $this->posts_model->field('a.*,c.user_login,c.user_nicename,b.listorder,b.tid');
		    $this->posts_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
		}
		$posts=$this->posts_model->select();
		foreach ($posts as $key => $value) {
			$posts[$key]['cat_name'] = M('terms')->alias("t")->join("__TERM_RELATIONSHIPS__ b ON t.term_id = b.term_id")->where("object_id='".$value['id']."'")->getField('t.name');
			if($value['smeta'])
			{
				$posts[$key]['smeta'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$value['smeta'];
			}
		}
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("posts",$posts);
		
	}
	
	// 获取文章分类树结构 select 形式
	private function _getTree(){
		$term_id=empty($_REQUEST['term'])?0:intval($_REQUEST['term']);
		$result = $this->terms_model->order(array("listorder"=>"asc"))->select();
		
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
			$r['visit'] = "<a href='#'>访问</a>";
			$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
			$r['id']=$r['term_id'];
			$r['parentid']=$r['parent'];
			$r['selected']=$term_id==$r['term_id']?"selected":"";
			$array[] = $r;
		}
		
		$tree->init($array);
		$str="<option value='\$id' \$selected>\$spacer\$name</option>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
	}
	
	// 获取文章分类树结构 
	private function _getTermTree($term=array()){
		$result = $this->terms_model->order(array("listorder"=>"asc"))->select();
		
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
			$r['visit'] = "<a href='#'>访问</a>";
			$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
			$r['id']=$r['term_id'];
			$r['parentid']=$r['parent'];
			$r['selected']=in_array($r['term_id'], $term)?"selected":"";
			$r['checked'] =in_array($r['term_id'], $term)?"checked":"";
			$array[] = $r;
		}
		
		$tree->init($array);
		$str="<option value='\$id' \$selected>\$spacer\$name</option>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
	}
	
	// 文章删除
	public function delete(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->posts_model->where(array('id'=>$id))->delete() !==false) {
				$this->term_relationships_model->where(array('object_id'=>$id))->delete();
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
}