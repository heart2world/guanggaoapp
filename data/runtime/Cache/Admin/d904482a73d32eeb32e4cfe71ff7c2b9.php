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
<style type="text/css">
.pic-list li {
	margin-bottom: 5px;
}
</style>

</head>
<body>
	<div class="wrap js-check-wrap" id="app">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('category/index');?>">分类管理</a></li>
			<li class="active"><a href="javascript:;">编辑分类</a></li>
		</ul>
		<form class="form-horizontal" id="tagforms" method="post" enctype="multipart/form-data">
			<fieldset>
				<div class="control-group">
					<label class="control-label">分类名称：</label>
					<div class="controls">
						<input type="text" name="catname" maxlength="5" value="<?php echo ($info["catname"]); ?>" placeholder="名称限制5字以内">
					</div>
				</div>
						
				<div class="control-group">
					<label class="control-label">分类图标：</label>
					<div class="controls">
						<input type="hidden" name="catimg" id="thumb" value="<?php echo ($info["catimg"]); ?>">
						<a href="javascript:upload_one_image('图片上传','#thumb');">	
							<?php if($info['catimg'] != ''): ?><img src="<?php echo ($info["catimg2"]); ?>" id="thumb-preview" width="100" height="100" style="cursor: hand" />	
							<?php else: ?>				
							<img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb-preview" width="100" height="100" style="cursor: hand" /><?php endif; ?>
						</a>
						<input type="button" class="btn btn-small" onclick="$('#thumb-preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb').val('');return false;" value="取消图片">
					</div><br/>
					<div style="margin-left: 180px;color: red;">建议图片尺寸：300*300</div>
				</div>
				<div class="control-group">
					<label class="control-label">排序：</label>
					<div class="controls">
						<input type="text" name="sortorder" maxlength="3" value="<?php echo ($info["sortorder"]); ?>" placeholder="请输入排序序号，序号越小越排前面">
					</div>
				</div>
				
			</fieldset>
			<div class="form-actions">
				<input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
				<input type="hidden" name="oldcatname" value="<?php echo ($info["catname"]); ?>">
				<input type="button" @click="add()" class="btn btn-primary" value="保存"/>
				<a class="btn" href="javascript:history.back(-1);"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script type="text/javascript" src="/public/js/common.js"></script>
	<script src="/public/js/vue.js"></script>
	<script src="/public/js/content_addtop.js"></script>
	<script src="/public/js/define_my.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
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
				     var tagvals=$('#tagforms').serialize();				
					$.ajax({
						url:'<?php echo U("Admin/category/edit_post");?>',
						data:tagvals,
						type:"POST",
						dataType:"json",
						success:function (res) {							
							if(res.status==0){
								$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
								setInterval(function(){
									location.href='<?php echo U("Admin/category/index");?>';
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