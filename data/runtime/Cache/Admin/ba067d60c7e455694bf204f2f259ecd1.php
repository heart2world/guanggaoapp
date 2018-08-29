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
			<li><a href="<?php echo U('member/index');?>">会员管理</a></li>	
			<li class="active"><a href="javascript:;">会员角色</a></li>	
			<li><a href="<?php echo U('member/level');?>">会员等级</a></li>	
			<li><a href="<?php echo U('member/leveldec');?>">会员等级说明</a></li>				
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Member/index');?>">	
			<a class="btn" style="background: #1dccaa;" href="<?php echo U('Member/addrole');?>">新增</a>
		</form>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th style="min-width: 50px;text-align: center;">ID</th>
						<th style="min-width: 100px;text-align: center;">角色名称</th>
						<th style="min-width: 100px;text-align: center;">角色描述</th>
						<th style="min-width: 100px;text-align: center;">操作</th>
					</tr>
				</thead>
				<?php if(is_array($role)): foreach($role as $key=>$vo): ?><tr>
                    <td style="text-align: center;"><b><?php echo ($vo["id"]); ?></b></td>
					<td style="text-align: center;"><?php echo ($vo["rolename"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["roledesc"]); ?></td>
					<td style="text-align: center;">						
						<a href="<?php echo U('Member/deleterole',array('id'=>$vo['id']));?>" class="btn btn-primary js-ajax-delete" data-msg="是否确认删除该角色？" style="padding: 2px 15px;color: white;background-color: #1dccaa;">删除</a>  
						<a href="<?php echo U('Member/editrole',array('id'=>$vo['id']));?>" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">编辑</a>  						
					</td>
				</tr><?php endforeach; endif; ?>				
			</table>
			<div class="pagination" style="float: right;"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	
</body>
</html>