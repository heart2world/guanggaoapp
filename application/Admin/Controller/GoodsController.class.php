<?php
// +----------------------------------------------------------------------
// | ThinkCMF 客户管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
use Common\Service\GoodsService;
use Think\Exception;
class GoodsController extends AdminbaseController {
    private $model;
    /**
     * 构造方法
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if (empty($this->_goodsService)) {
            $this->_goodsService = new GoodsService();
        }
        $this->model=M('Goods');
    }
	// 后台客户管理列表
	public function index(){				
		$where=array();	
		$this->goods =M('goods');
		$keyword=I('keyword','','trim');
		if(!empty($keyword)){
		    $where['goodsname']=array('like',"%$keyword%");
		}		
		$count=$this->goods->where($where)->count();			
		$page = $this->page($count, 20);			
		$goods=$this->goods
				->where($where)
				->limit($page->firstRow , $page->listRows)
				->order('sortorder asc,createtime desc')
				->select();
		$catnamestr ='';
        foreach ($goods as $key => $value) {
			$catids =explode(',',$value['catid']);
			foreach($catids as $k=>$v)
			{
				$catname=M('category')->where("id='".$v."'")->getField('catname');
				$catnamestr .= $catname.',';
			}
			if($catnamestr)
			{
				$catnamestr =substr($catnamestr,'0','-1');
			}
            $goods[$key]['catname'] = $catnamestr;
			$catnamestr ='';
        }
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("list",$goods);
		$this->display();
	}	
    public function add()
    {
        $catlist =M('category')->where("status=1")->select();
        $this->assign('catlist',$catlist);
        $this->display();
    }
    public function add_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['goodsname']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请输入商品名称'));
            }
            if(empty($pdata['thumb_img']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请上传缩略图'));
            }
            if(empty($pdata['catids']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请选择商品分类'));
            }
            if(!empty($pdata['photos_alt']) && !empty($pdata['photos_url'])){
                if(count($pdata['photos_url']) >5)
                {
                    $this->ajaxReturn(array('status'=>1,'msg'=>'最多上传5张商品图片'));
                }
                foreach ($pdata['photos_url'] as $key=>$url){
                    $url=makeoldImg($url,750,750);
                    $_POST['smeta']['photo'][]=array("url"=>$url,"alt"=>$pdata['photos_alt'][$key]);
                }
            }
            $pdata['createtime']=time();  
			$pdata['catid'] =implode(',',$pdata['catids']);
            $pdata['goodsdesc'] =strcontentjs(htmlspecialchars_decode($pdata['goodsdesc']));
            $pdata['imgs']=json_encode($_POST['smeta']);  
            $goodsnumber=$pdata['inventory']; 
            try{
                $this->model->startTrans();
                $good_id= $this->model->add($pdata);
                foreach ($pdata['catids'] as $key => $value) {
                    $catstr['cat_id'] =$value;
                    $catstr['goods_id'] =$good_id;
                    M('goods_cat')->add($catstr);
                }
                $spec_ids=$_POST['spec_id'];                    
                $option_data=json_decode($_POST['optionArray'],true);
                if(count($spec_ids) > 0)
                {
                  //商品规格名称添加
                    if(!empty($pdata['spec_title'])){
                        foreach ($pdata['spec_title'] as $key=>$item) {
                            if(empty($item))
                            {
                                $this->ajaxReturn(array('status'=>1,'msg'=>'规格名称不能为空'));
                            }
                            $spec['goods_id']=$good_id;
                            $spec['title']=$item;
                            $spec['createtime']=time();
                            $spec['content']=serialize($pdata['spec_item_title_'.$key]);
                            $spec_id=M('GoodsSpec')->add($spec);
                            if(count($pdata['spec_item_title_'.$key])==0)
                            {
                                $this->ajaxReturn(array('status'=>1,'msg'=>'请添加规格项'));
                            }
                            foreach ($pdata['spec_item_title_'.$key] as $value) {
                                if(empty($value))
                                {
                                    $this->ajaxReturn(array('status'=>1,'msg'=>'规格名称不能为空'));
                                }   
                                $spec_item['goods_id']=$good_id;
                                $spec_item['specid']=$spec_id;
                                $spec_item['title']=$value;
                                $spec_item['createtime']=time();
                                $spec_item_rel=M('GoodsSpecItem')->add($spec_item);
                            }
                        }
                    }
                    //吊牌价，售价
                    if(!empty($option_data['option_dropprice'])){
                            $goodsnumber =0;
                            for ($i=0;$i<count($option_data['option_dropprice']);$i++){
                                $option_arr['goods_id']=$good_id;
                                $option_arr['title']=$option_data['option_title'][$i];
                                $sp_arr=explode('+',$option_arr['title']);

                                $sp_str='';
                                foreach ($sp_arr as $key=>$item) {
                                    $sp_id=M('GoodsSpecItem')->where(['goods_id'=>$good_id,'title'=>$item])->getField('id');
                                    $sp_str.=$sp_id;
                                    if($key<count($sp_arr)-1){
                                        $sp_str.='_';
                                    }

                                }
                                if(empty($option_data['option_dropprice'][$i])){
                                  
                                    $this->ajaxReturn(array('msg'=>'商品现价没有设置','status'=>1));
                                }
                                if(empty($option_data['option_settleprice'][$i])){
                                  
                                    $this->ajaxReturn(array('msg'=>'商品成本价没有设置','status'=>1));
                                }
                                if(empty($option_data['option_weight'][$i])){
                                  
                                    $this->ajaxReturn(array('msg'=>'商品单位没有设置','status'=>1));
                                }
                                $option_arr['specs_item']=$sp_str;
                                $option_arr['sale_price']=$option_data['option_dropprice'][$i] ?$option_data['option_dropprice'][$i] :0 ;
                                $option_arr['settleprice']=$option_data['option_settleprice'][$i] ? $option_data['option_settleprice'][$i]: 0;
                                $option_arr['unit']=$option_data['option_weight'][$i] ? $option_data['option_weight'][$i]: 0;
                                $option_arr['inventory']=$option_data['option_inventory'][$i] ? $option_data['option_inventory'][$i]: 0;
                                $option_arr['createtime']=time();
                                $goodsnumber +=$option_arr['inventory'];
                                $rel=M('GoodsOption')->add($option_arr);
                        }
                        M('goods')->where("id='$good_id'")->setField('inventory',$goodsnumber);
                    }
                } 
            }
            catch(Exception $e){
                $this->model->rollback();
                $this->error($e->getMessage());
            }
            $this->model->commit();
            $this->ajaxReturn(array('status'=>0,'msg'=>'添加成功'));  
        }
    }
    public function edit()
    {
        $id =I('id','','intval');
        if(IS_POST){
            $id=I('post.id');
            $tpl_item=$this->show_spec_tpl($id);
            $this->ajaxReturn(['status'=>1,'tpl_str'=>$tpl_item]);
        }

        $goods =M('goods')->find($id);
        if($goods['thumb_img'])
        {
            $goods['thumb_img2'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$goods['thumb_img'];
        }
        $this->assign("imgs",json_decode($goods['imgs'],true));

        //价格显示
        $spec=M('GoodsSpec')->field('id,title')->where(['goods_id'=>$id])->select();
        foreach ($spec as &$item) {
            $spec_item=M('GoodsSpecItem')->field('id,title')->where(['goods_id'=>$id,'specid'=>$item['id']])->select();
            foreach ($spec_item as &$value) {
                $value['show']=0;
            }
            $item['items']=$spec_item;
            unset($item['content']);
        }

        $goodsOption=M('GoodsOption')->where(['goods_id'=>$id])->select();
        if (empty($goodsOption)) {
            $option = 0;
        }else{
            $option = 1;
        }
        $this->assign('goods_option',json_encode($goodsOption));      
        $this->assign('option',$option);
        $this->assign('specs',json_encode($spec));
        $this->assign('goods',$goods);
        $catlist =M('category')->where("status=1")->select();
        $catids =explode(',',$goods['catid']);
        foreach ($catlist as $key => $value) {
           
            if(in_array($value['id'], $catids))
            {
                $catlist[$key]['isdo'] =1;
            }else
            {
                $catlist[$key]['isdo'] =0;
            }
            
        }
        $this->assign('catlist',$catlist); 
        $this->display();
        
    }
   
    public function edit_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            
            if(empty($pdata['goodsname']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请输入商品名称'));
            }
            if(empty($pdata['thumb_img']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请上传缩略图'));
            }
            if(empty($pdata['catids']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请选择商品分类'));
            }
            if(!empty($pdata['photos_alt']) && !empty($pdata['photos_url'])){
                if(count($pdata['photos_url']) >5)
                {
                    $this->ajaxReturn(array('status'=>1,'msg'=>'最多上传5张商品图片'));
                }
                foreach ($pdata['photos_url'] as $key=>$url){

                    if(strpos($url,'images_') === false)
                    {
                        $url= makeoldImg($url,750,750);
                    }                    
                    $_POST['smeta']['photo'][]=array("url"=>$url,"alt"=>$pdata['photos_alt'][$key]);
                }
            }
            $pdata['goodsdesc'] =strcontentjs(htmlspecialchars_decode($pdata['goodsdesc']));
            $pdata['imgs']=json_encode($_POST['smeta']);   
            $goodsnumber =$pdata['inventory'];
            $optionArray = json_decode($_POST['optionArray'], true);  
            file_put_contents('aa1.txt', var_export($_POST['spec_id'],true));
            $pdata['catid'] =implode(',',$pdata['catids']);
            try{
                $this->model->startTrans();
                $good_id = $pdata['id'];
                $result= $this->model->where("id='".$pdata['id']."'")->save($pdata);
                M('goods_cat')->where("goods_id='".$pdata['id']."'")->delete();
                foreach ($pdata['catids'] as $key => $value) {
                    $catstr['cat_id'] =$value;
                    $catstr['goods_id'] =$good_id;
                    M('goods_cat')->add($catstr);
                }
                $spec_ids=$_POST['spec_id'];  

                $spec_titles = $_POST['spec_title'];    
                if(count($spec_ids) >0)
                {
                                   
                    $len = count($spec_ids);
                    $k=0;
                    $specids = array();
                    $spec_items = array();
                    while ( $k<$len) {
                        $spec_id = '';
                        $get_spec_id = $spec_ids[$k];
                        $a = array('goods_id' => $good_id, 'title' => $spec_titles[$get_spec_id]);
                        if(empty($a['title']))
                        {
                            $this->ajaxReturn(array('status'=>1,'msg'=>'规格名称不能为空'));
                        }
                        if (is_numeric($get_spec_id)) {
                            M('goods_spec')->where("id='$get_spec_id'")->save($a);
                            $spec_id = $get_spec_id;
                        }
                        else {
                            $a['createtime'] =time();                            
                            $spec_id=M('goods_spec')->add($a);
                        }
                        $spec_item_ids = $_POST['spec_item_id_' . $get_spec_id];                        
                        $spec_item_titles = $_POST['spec_item_title_' . $get_spec_id];                        
                        $itemlen = count($spec_item_ids);
                        $itemids = array();
                        $n = 0;
                        if($itemlen == 0)
                        {
                            $this->ajaxReturn(array('status'=>1,'msg'=>'请添加规格项'));                            
                        }
                        while ($n < $itemlen) {
                            $item_id = '';
                            $get_item_id = $spec_item_ids[$n];
                            $d = array('specid' => $spec_id,'goods_id'=>$good_id,'title' => $spec_item_titles[$n]);
                            if(empty($d['title']))
                            {
                                $this->ajaxReturn(array('status'=>1,'msg'=>'规格名称不能为空'));
                            }
                            if (is_numeric($get_item_id)) {
                                M('goods_spec_item')->where("id='$get_item_id'")->save($d);
                                $item_id = $get_item_id;
                            }
                            else {
                                $d['createtime']=time();
                                $item_id = M('goods_spec_item')->add($d);
                            }

                            $itemids[] = $item_id;
                            $itemtitles[] = $spec_item_titles[$n];
                            $d['get_id'] = $get_item_id;
                            $d['id'] = $item_id;
                            $spec_items[] = $d;
                            ++$n;
                        }
                        
                        if (0 < count($itemids)) {
                            M('goods_spec_item')->where('specid='.$spec_id.' and id not in ('.implode(',',$itemids).')')->delete();
                        }
                        else {
                             M('goods_spec_item')->where("specid='$spec_id'")->delete();
                        }

                        M('goods_spec')->where("id='$spec_id'")->save(array('content'=>serialize($itemtitles)));
                        $specids[] = $spec_id;
                        $itemtitles=array();
                        ++$k;
                    }
                    
                    if (0 < count($specids)) {
                        M('goods_spec')->where('goods_id='.$good_id.' and id not in('.implode(',',$specids).')')->delete();
                    }
                    else {
                        M('goods_spec')->where('goods_id='.$good_id.'')->delete();
                    }
                   
                    if(!empty($optionArray['option_dropprice'])){
                        $goodsnumber =0;
                        for ($i=0;$i<count($optionArray['option_dropprice']);$i++){
                            if(empty($optionArray['option_dropprice'][$i])){
                              
                                $this->ajaxReturn(array('msg'=>'商品现价没有设置','status'=>1));
                            }
                            if(empty($optionArray['option_settleprice'][$i])){
                                
                                $this->ajaxReturn(array('msg'=>'商品成本价没有设置','status'=>1));
                            }
                             if(empty($optionArray['option_weight'][$i])){
                                
                                $this->ajaxReturn(array('msg'=>'商品单位没有设置','status'=>1));
                            }
                            $goodsnumber += $optionArray['option_inventory'][$i];
                        }
                    }
                    $option_idss = $optionArray['option_ids'];
                    $len = count($option_idss);
                    $optionids = array();
                    $k = 0;

                    while ($k < $len) {
                        $option_id = '';
                        $ids = $option_idss[$k];
                        $get_option_id = $optionArray['option_id'][$k];
                        $idsarr = explode('_', $ids);
                        $newids = array();

                        foreach ($idsarr as $key => $ida) {
                            foreach ($spec_items as $it) {
                                if ($it['get_id'] == $ida) {
                                    $newids[] = $it['id'];
                                    break;
                                }
                            }
                        }

                        $newids = implode('_', $newids);
                        $a = array('title' => $optionArray['option_title'][$k], 'sale_price' => $optionArray['option_dropprice'][$k], 'settleprice' => $optionArray['option_settleprice'][$k], 'unit' => $optionArray['option_weight'][$k],'inventory'=> $optionArray['option_inventory'][$k],'goods_id' => $good_id, 'specs_item' => $newids);

                        if (empty($get_option_id)) {
                            $a['createtime']=time();
                            $option_id = M('goods_option')->add($a);
                        }
                        else {
                            M('goods_option')->where("id='$get_option_id'")->save($a);
                            $option_id = $get_option_id;
                        }

                        $optionids[] = $option_id;
                        ++$k;
                    }
                    if (0 < count($optionids)) 
                    {
                        M('goods_option')->where('goods_id='.$good_id.' and id not in ('.implode(',',$optionids).')')->delete();
                    }
                    else 
                    {
                        M('goods_option')->where('goods_id='.$good_id.'')->delete();
                    }

                    M('goods')->where("id='$good_id'")->setField('inventory',$goodsnumber);
                
                }
                else
                {
                    
                    M('goods_spec')->where("goods_id='$good_id'")->delete();
                    M('goods_spec_item')->where("goods_id='$good_id'")->delete();
                    M('goods_option')->where("goods_id='$good_id'")->delete();
                }
            }
            catch(Exception $e){
                $this->model->rollback();
                $this->ajaxReturn(array('status'=>1,'msg'=>$e->getMessage())); 
            }
            $this->model->commit();           
            $this->ajaxReturn(array('status'=>0,'msg'=>'编辑成功')); 
        }
    }
    public function delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if (M('goods')->where(array('id'=>$id))->delete() !==false) {
                M('goods_cat')->where("goods_id='$id'")->delete();
                M('goods_option')->where("goods_id='$id'")->delete();
                M('goods_spec')->where("goods_id='$id'")->delete();
                M('goods_spec_item')->where("goods_id='$id'")->delete();
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        
    }
    public function ban(){
        $id = I('get.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('goods')->where(array("id"=>$id))->setField('issale','0');
    		if ($result!==false) {
    			$this->success("下架成功！", U("goods/index"));
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
    		$result = M('goods')->where(array("id"=>$id))->setField('issale','1');
    		if ($result!==false) {
    			$this->success("上架成功！", U("goods/index"));
    		} else {
    			$this->error('上架失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function add_spec_tpl(){
        $id_str=strtolower(func_randStrWithCode(20));
        $html='';
        $html.='<div class="spec_item" id="spec_'.$id_str.'">
                    <div style="border:1px solid #e7eaec;padding:10px;margin-bottom: 10px;">
                        <input name="spec_id[]" type="hidden" class="form-control spec_id" value="'.$id_str.'">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input name="spec_title['.$id_str.']" type="text" class="form-control  spec_title valid" value="" placeholder="规格名称 (比如: 颜色)" aria-invalid="false">
                                    <div class="input-group-btn">
                                        <a href="javascript:;" id="add-specitem-'.$id_str.'" specid="'.$id_str.'" class="btn btn-info add-specitem" onclick="addSpecItem(\''.$id_str.'\')"><i class="fa fa-plus"></i> 添加规格项</a>
                                        <a href="javascript:void(0);" class="btn btn-danger" onclick="removeSpec(\''.$id_str.'\')"><i class="fa fa-remove"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div id="spec_item_'.$id_str.'" class="spec_item_items">

                                    


                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
        $this->ajaxReturn(array('data'=>$html,'status'=>0));
    }
	
     function add_spec_item_tpl(){
        $specid=I('get.specid');
        $html='';
        $id_str=strtolower(func_randStrWithCode(10));
        $html.='<div class="spec_item_item" style="float:left;margin:5px;width:250px; position: relative">';
        $html.='<input type="hidden" class="form-control spec_item_show" name="spec_item_show_'.$specid.'[]" value="1">
                     <input type="hidden" class="form-control spec_item_id" name="spec_item_id_'.$specid.'[]" value="'.$id_str.'">
                     <div class="input-group">
                    <input type="text" class="form-control spec_item_title valid" onblur="refreshOptions();" name="spec_item_title_'.$specid.'[]" value="" aria-invalid="false">
                      <span class="input-group-addon">
                    <a href="javascript:;" onclick="removeSpecItem(this)" title="删除"><i class="fa fa-times"></i></a>

                </span>
                </div>
            </div>';
       $this->ajaxReturn(array('data'=>$html,'status'=>0));
    }


    function show_spec_tpl($good_id){
        $spec_array=M('GoodsSpec')->where(['goods_id'=>$good_id])->select();
        $html='';
        foreach ($spec_array as &$item) {
            $id_str=$item['id'];
            $specs=unserialize($item['content']);
            $html.='<div class="spec_item" id="spec_'.$id_str.'">
                        <div style="border:1px solid #e7eaec;padding:10px;margin-bottom: 10px;">
                            <input name="spec_id[]" type="hidden" class="form-control spec_id" value="'.$id_str.'">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input name="spec_title['.$id_str.']" type="text" class="form-control  spec_title valid" value="'.$item['title'].'" aria-invalid="false">
                                        <div class="input-group-btn">
                                            <a href="javascript:;" id="add-specitem-'.$id_str.'" specid="'.$id_str.'" class="btn btn-info add-specitem" onclick="addSpecItem(\''.$id_str.'\')"><i class="fa fa-plus"></i> 添加规格项</a>
                                            <a href="javascript:void(0);" class="btn btn-danger" onclick="removeSpec(\''.$id_str.'\')"><i class="fa fa-remove"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                    <div class="col-md-12"><div id="spec_item_'.$id_str.'" class="spec_item_items">';
            foreach ($specs as $spec) {
                $specid=$item['id'];
                $itemid = M('goods_spec_item')->where("specid='$specid' and title='$spec'")->getField('id');
                $html.='<div class="spec_item_item" style="float:left;margin:5px;width:250px; position: relative">';
                $html.='<input type="hidden" class="form-control spec_item_show" name="spec_item_show_'.$specid.'[]" value="1">
                        <input type="hidden" class="form-control spec_item_id" name="spec_item_id_'.$specid.'[]" value="'.$itemid.'">
                        <div class="input-group">
                        <input type="text" class="form-control spec_item_title valid" name="spec_item_title_'.$specid.'[]" value="'.$spec.'" aria-invalid="false">
                        <span class="input-group-addon">
                        <a href="javascript:;" onclick="removeSpecItem(this)" title="删除"><i class="fa fa-times"></i></a>
                        </span>
                        </div>
                        </div>';
            }
            $html.=' </div></div></div> </div></div>';
        }
        return $html;
    }
}