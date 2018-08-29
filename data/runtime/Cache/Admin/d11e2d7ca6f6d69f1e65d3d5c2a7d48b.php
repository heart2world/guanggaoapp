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
</head>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">商品管理</a></li>
			<li><a href="<?php echo U('goods/add');?>">添加商品</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('goods/index');?>">	
			商品名称： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ((isset($formget["keyword"]) && ($formget["keyword"] !== ""))?($formget["keyword"]):''); ?>" placeholder="请输入商品名称">&nbsp;&nbsp;
			
			<input type="submit" class="btn btn-primary" value="查询" />
			<a class="btn btn-danger" href="<?php echo U('goods/index');?>">清空</a>
		</form>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th style="min-width: 50px;text-align: center;">ID</th>
						<th style="min-width: 150px;text-align: center;">商品名称</th>
						<th style="min-width: 100px;text-align: center;">所属分类</th>
						<th style="min-width: 80px;text-align: center;">库存</th>
						<th style="min-width: 30px;text-align: center;">排序</th>
						<th style="min-width: 30px;text-align: center;">推荐</th>
						<th style="min-width: 50px;text-align: center;">状态</th>						
						<th style="min-width: 200px;text-align: center;">操作</th>
					</tr>
				</thead>
				<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                    <td style="text-align: center;"><b><?php echo ($vo["id"]); ?></b></td>
					<td style="text-align: center;"><?php echo ($vo["goodsname"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["catname"]); ?></td>	
					<td style="text-align: center;"><?php echo ($vo["inventory"]); ?></td>				
					<td style="text-align: center;"><?php echo ($vo["sortorder"]); ?></td>
					<td style="text-align: center;"><?php if($vo['isindex'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>	
					<td style="text-align: center;"><?php if($vo['issale'] == 1): ?>上架<?php else: ?>下架<?php endif; ?></td>					
					<td style="text-align: center;">
						<a href="<?php echo U('goods/delete',array('id'=>$vo['id']));?>" class="btn btn-primary js-ajax-delete" style="padding: 2px 15px;color: white;background-color: #1dccaa;"><?php echo L('DELETE');?></a>
						<a href="<?php echo U('goods/edit',array('id'=>$vo['id']));?>" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;"><?php echo L('EDIT');?></a>  						
						<?php if($vo['issale'] == 1): ?><a href="<?php echo U('goods/ban',array('id'=>$vo['id']));?>" class="btn btn-primary js-ajax-dialog-btn" style="padding: 2px 15px;color: white;background-color: #1dccaa;"  data-msg="确认下架该商品吗？">下架</a> 
						<?php else: ?>
							<a href="<?php echo U('goods/cancelban',array('id'=>$vo['id']));?>" class="btn btn-primary js-ajax-dialog-btn" style="padding: 2px 15px;color: white;background-color: #1dccaa;"  data-msg="确认上架该商品吗？">上架</a><?php endif; ?> 
						
					</td>
				</tr><?php endforeach; endif; ?>				
			</table>
			<div class="pagination" style="float: right;"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	
</body>
</html>