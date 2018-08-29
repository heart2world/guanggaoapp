<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
	<script type="text/javascript">
	//全局变量
	var GV = {
	    ROOT: "/",
	    WEB_ROOT: "/",
	    JS_ROOT: "public/js/",
	    APP:'<?php echo (MODULE_NAME); ?>'/*当前应用名*/
	};
	</script>
    <script src="/public/js/jquery.js"></script>
    <script src="/public/js/wind.js"></script>
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <script>
    	$(function(){
    		$("[data-toggle='tooltip']").tooltip();
    	});
    </script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>
<script src="/public/js/wz.js?v=<?php echo ($ss); ?>"></script>
<link href="/public/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
<link href="/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/public/css/animate.min.css" rel="stylesheet">
<link href="/public/css/style.min.css?v=4.0.0" rel="stylesheet">
<style type="text/css">
input[type="text"]{
        height: 34px;
    }
input[type="number"]{
    height: 34px;
}
.pic-list li {
	margin-bottom: 5px;
}
td{
	text-align: center;
}
.close-box{
	background-color: white;
	color: black;
	position: relative;
	margin-left: -40px;
	margin-right: 20px;
	margin-bottom:10px;
	font-size: large;
	font-weight: bold
}
</style>
<script type="text/html" id="photos-item-wrapper">
	<li id="savedimage{id}">
		<input id="photo-{id}" type="hidden" name="photos_url[]" value="{filepath}"> 
		<input id="photo-{id}-name" type="text" name="photos_alt[]" value="{name}" style="width: 200px;" title="图片名称">
		<img id="photo-{id}-preview" src="{url}" style="height:36px;width: 36px;" onclick="parent.image_preview_dialog(this.src);">
		<a href="javascript:upload_one_image('图片上传','#photo-{id}');">替换</a>
		<a href="javascript:(function(){$('#savedimage{id}').remove();})();">移除</a>
	</li>
</script>
</head>
<body>
	<div class="wrap js-check-wrap" id="app">
		<ul class="nav nav-tabs">
	        <li class="active" ><a href="#A" data-toggle="tab">商品信息</a></li>
	        <li ><a href="#B" data-toggle="tab">商品规格</a></li>
	        <li><a href="#C" data-toggle="tab">商品详情</a></li>
	    </ul>
		<form class="form-horizontal" id="tagforms" method="post" enctype="multipart/form-data">
			<fieldset>
	            <div class="tabbable">
	                <div class="tab-content">
	                    <div class="tab-pane active" id="A">
	                        <fieldset>
								<div class="control-group" style="margin-top: 20px;">
									<label class="control-label">商品名称：</label>
									<div class="controls">
										<input type="text" name="goodsname" maxlength="20" placeholder="名称限制20字以内">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">商品描述：</label>
									<div class="controls">
										<textarea name="goodsbrief" maxlength="200" style="width: 342px;"></textarea>
										
									</div>
								</div>	
								<div class="control-group">
									<label class="control-label">缩略图：</label>
									<div class="controls">
										<input type="hidden" name="thumb_img" id="thumb" value="">
										<a href="javascript:upload_one_image('图片上传','#thumb');">							
											<img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb-preview" width="100" height="100" style="cursor: hand" />
										</a>
										<input type="button" class="btn btn-small" onclick="$('#thumb-preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb').val('');return false;" value="取消图片">
									</div><br/>
									<div style="margin-left: 180px;color: red;">建议图片尺寸：300*300</div>
								</div>

								<div class="control-group">
									<label class="control-label">商品图片：</label>
									<div class="controls">
										<ul id="photos" class="pic-list unstyled"></ul>
										<a href="javascript:upload_multi_image('图片上传','#photos','photos-item-wrapper');" class="btn btn-small">选择图片</a>
									</div><br/>
									<div style="margin-left: 180px;color: red;">建议图片尺寸：750*750,最多上传5张图片</div>
								</div>

								<div class="control-group">
									<label class="control-label">商品分类：</label>
									<div class="controls">
										<?php if(is_array($catlist)): $key = 0; $__LIST__ = $catlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($key % 2 );++$key;?><span style="padding: 5px 5px;"><input type="checkbox" style="margin-top: -3px;" name="catids[]" value="<?php echo ($va["id"]); ?>"><?php echo ($va["catname"]); ?></span><?php if(($key+1)%8 == 1): ?><br/><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</div>
								</div>
												
								<div class="control-group">
									<label class="control-label">排序：</label>
									<div class="controls">
										<input type="text" name="sortorder" maxlength="3" value="0" placeholder="请输入排序序号，序号越小越排前面">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">推荐：</label>
									<div class="controls">
										<input type="radio" name="isindex" value="1" checked> 是&nbsp;&nbsp;&nbsp;
										<input type="radio" name="isindex" value="0"> 否&nbsp;
									</div>
								</div>
								<div class="control-group">									
									<label class="control-label">价格/单位：</label>
									<div class="controls">
										现价&nbsp;<input type="number" name="price" style="width:120px;height: 34px;" value="">&nbsp;
										成本价&nbsp;<input type="number" name="settleprice" style="width:120px;height: 34px;" value="">&nbsp;
										/&nbsp;<input type="text" style="width:100px;" name="unit" placeholder='例如：米' value="">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">销量：</label>
									<div class="controls">
										<input type="number" name="number" min="0" placeholder="请输入销量">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">库存：</label>
									<div class="controls">
										<input type="number" min="0" name="inventory" placeholder="请输入库存">
									</div>
								</div>
							</fieldset>
						</div>
						<div class="tab-pane" id="B">
	                        <fieldset>
	                            <div class="control-group" style="margin-top: 20px">
	                        
	                                <div class="controls" style="margin-left:100px;">
	                                    <input type="button" class="btn btn-info add-spec-name" onclick="addSpec()" value="添加规格">
	                                    <a class="btn btn-primary" onclick="refreshOptions();">刷新规格</a><span style="margin-left: 10px;font-size: 14px;">注：如商品规格变动，请刷新规格</span>
	                                </div>
	                            </div>
	                            <div class="control-group">
	                          
	                                <div class="controls" style="width: 80%;margin-left:100px;">
	                                    <div id="specs" class="ui-sortable">
	                                    </div>
	                                </div>
	                            </div>

	                            <div class="control-group">
	                              
	                                <div class="controls" style="width: 80%;margin-left: 100px;">
	                                    <div id="options" style="padding: 0"></div>

	                                </div>
	                            </div>
	                         

	                            <input type="hidden" name="optionArray" id="optionArray" value="">
	                        </fieldset>
	                    </div>

						<div class="tab-pane" id="C">
	                        <fieldset>
	                        	<div class="control-group" style="margin-top: 20px;">
									<label class="control-label">详情：</label>
									<div class="controls" style="width: 900px;">
										<script type="text/plain"  id="content" name="goodsdesc"></script>
									</div>
								</div>
	                        </fieldset>
	                    </div>
					
					 </div>
	            </div>
				<div class="form-actions">
					<input type="button" @click="add()" class="btn btn-primary" value="保存"/>
					<a class="btn" href="javascript:history.back(-1);"><?php echo L('BACK');?></a>
				</div>
			</fieldset>
		</form>
	</div>
	<script type="text/javascript" src="/public/js/common.js"></script>
	<script src="/public/js/vue.js"></script>
	<script src="/public/js/content_addtop.js"></script>
	<script src="/public/js/define_my.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
		//编辑器路径定义
		var editorURL = GV.WEB_ROOT;
	</script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.all.min.js"></script>
	<script type="text/javascript">
		$(function() {
			Wind.use('validate','artDialog', function() {
				//javascript

				//编辑器
				editorcontent = new baidu.editor.ui.Editor();
				editorcontent.render('content');
				try {
					editorcontent.sync();
				} catch (err) {
				}
				//增加编辑器验证规则
				jQuery.validator.addMethod('editorcontent', function() {
					try {
						editorcontent.sync();
					} catch (err) {
					}
					return editorcontent.hasContents();
				});
			});
			////-------------------------
		});
	</script>
	<script>
		var app = new Vue({
			el:"#app",
			data:{
				info:{},				
			},
			created:function () {
			},
			methods:{
				add:function () {	
					 optionArray();    
				     var tagvals=$('#tagforms').serialize();				
					$.ajax({
						url:'<?php echo U("Admin/goods/add_post");?>',
						data:tagvals,
						type:"POST",
						dataType:"json",
						success:function (res) {							
							if(res.status==0){
								$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
								setInterval(function(){
									location.href='<?php echo U("Admin/goods/index");?>';
								},3000)
							}
							else {
								$.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
							}
						}

					})
				}
			}
		});	

	</script>
</body>
</html>