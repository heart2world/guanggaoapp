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
<link href="/public/js/jedate/skin/jedate.css" rel="stylesheet">
<style>
	.table tr th, .table tr td {
		text-align: center;
	}
</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('Management/index');?>">销量排行</a></li>
			<li class="active"><a href="javascript:;">销量转化率</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Management/cvr');?>">
			商品：
			<input type="text" name="goodsname" autocomplete="off" style="width: 165px;" placeholder="请输入商品名称" value="<?php echo ($goodsname); ?>">
			<input type="submit" class="btn btn-primary" value="查询" />
			<a class="btn btn-primary" href="<?php echo U('Management/cvr');?>">清空</a>
			<a class="btn btn-danger" href="<?php echo U('Management/export_cvr',array('goodsname'=>$goodsname));?>">导出Excel</a>
		</form>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th style="min-width: 100px;text-align: center;">商品名称</th>
						<th style="min-width: 30px;text-align: center;">访问次数</th>
						<th style="min-width: 100px;text-align: center;">购买件数</th>
						<th style="min-width: 100px;text-align: center;">转化率</th>
					</tr>
				</thead>
				<?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
                    <td style="text-align: center;"><?php echo ($vo["goodsname"]); ?></td>
                    <td style="text-align: center;"><?php echo ($vo["clickcount"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["number"]); ?></td>
                    <td style="text-align: center;"><?php echo ($vo["cvr"]); ?>%</td>
				</tr><?php endforeach; endif; ?>
				<?php if(count($data) == 0): ?><tr><td colspan="4">暂无数据</td></tr><?php endif; ?>
			</table>
			<div class="pagination" style="float: right;"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>