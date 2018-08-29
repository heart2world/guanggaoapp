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
	.laydate-icon {
		background: url('/public/js/jedate/skin/jedate.png') no-repeat right;
	}

	.time {
		width: 249px;
		height: 15px;
		line-height: 15px;
		padding: 6px 0 6px 10px;
		border: 1px solid #C1C1C1;
	}

	.table tr th, .table tr td {
		text-align: center;
	}
</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">订单管理</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Orders/index');?>">
			订单号：<input type="text" name="orderno" style="width: 150px;" value="<?php echo ($orderno); ?>" placeholder="请输入订单号">&nbsp;
			时间：
			<input class="time laydate-icon" type="text" name="start_time" autocomplete="off" id="start_time" style="cursor: pointer;width: 165px;" placeholder="请选择日期" value="<?php echo ($start_time); ?>">
			<input class="time laydate-icon" type="text" name="end_time" autocomplete="off" id="end_time" style="cursor: pointer;width: 165px;" placeholder="请选择日期" value="<?php echo ($end_time); ?>">&nbsp;
			状态：<select name="status" id="status" style="width: 100px;">
					<option value="">全部</option>
					<option value="2">待支付</option>
					<option value="3">待发货</option>
					<option value="4">待收货</option>
					<option value="1">已完成</option>
		  	 	 </select>&nbsp;&nbsp;
			支付方式：<select name="paytype" id="paytype" style="width: 110px;">
						<option value="">全部</option>
						<option value="3">余额支付</option>
						<option value="1">微信支付</option>
						<option value="2">支付宝支付</option>
						<option value="4">赊账</option>
						<option value="5">赊账/已还款</option>
					</select>&nbsp;&nbsp;
			<input name="mid" type="hidden" value="<?php echo ($mid); ?>">
			<input type="submit" class="btn btn-primary" value="查询" />
			<a class="btn btn-danger" href="<?php echo U('Orders/export',array('orderno'=>$orderno,'start_time'=>$start_time,'end_time'=>$end_time,'status'=>$status,'paytype'=>$paytype,'member_id'=>$mid));?>">导出Excel</a>
		</form>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th style="min-width: 30px;text-align: center;">ID</th>
						<th style="min-width: 100px;text-align: center;">时间</th>
						<th style="min-width: 150px;text-align: center;">订单号</th>
						<th style="min-width: 100px;text-align: center;">商品名称</th>
						<th style="min-width: 30px;text-align: center;">数量</th>
						<th style="min-width: 100px;text-align: center;">购买者</th>
						<th style="min-width: 100px;text-align: center;">实付金额</th>
						<th style="min-width: 100px;text-align: center;">支付方式</th>
						<th style="min-width: 50px;text-align: center;">状态</th>
						<th style="min-width: 200px;text-align: center;">操作</th>
					</tr>
				</thead>
				<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                    <td style="text-align: center;"><?php echo ($vo["id"]); ?></td>
                    <td style="text-align: center;"><?= date("Y-m-d H:i:s",$vo['create_time']) ?></td>
                    <td style="text-align: center;"><?php echo ($vo["orderno"]); ?></td>
                    <td style="text-align: center;"><?php echo ($vo["goods_name"]); ?></td>
                    <td style="text-align: center;"><?php echo ($vo["number"]); ?></td>
					<td style="text-align: center;">
						<?php echo ($vo["nickname"]); if($vo['remark1'] != ''): ?>(<?php echo ($vo["remark1"]); ?>)<?php endif; ?><br/><?php echo ($vo["mobile"]); ?>
					</td>
                    <td style="text-align: center;">￥<?php echo ($vo["paymoney"]); ?></td>
                    <td style="text-align: center;">
						<?php if($vo["paytype"] == 1): ?>微信支付<?php endif; ?>
						<?php if($vo["paytype"] == 2): ?>支付宝支付<?php endif; ?>
						<?php if($vo["paytype"] == 3): ?>余额支付<?php endif; ?>
						<?php if($vo["paytype"] == 4): ?>赊账<?php endif; ?>
						<?php if($vo["paytype"] == 5): ?>赊账/已还款<?php endif; ?>
					</td>
                    <td style="text-align: center;">
						<?php if($vo["status"] == 1): ?>已完成<?php endif; ?>
						<?php if($vo["status"] == 2): ?>待支付<?php endif; ?>
						<?php if($vo["status"] == 3): ?>待发货<?php endif; ?>
						<?php if($vo["status"] == 4): ?>待收货<?php endif; ?>
					</td>
                    <td style="text-align: center;">
						<a class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;" href="<?php echo U('Orders/detail',array('id'=>$vo['id']));?>">查看详情</a>
						<?php if($vo["status"] == 3): ?><a class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;" onclick="confirm_deliver('<?php echo ($vo["id"]); ?>')">确认发货</a><?php endif; ?>
						<?php if($vo["status"] == 4): ?><a class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;" onclick="confirm_take_deliver('<?php echo ($vo["id"]); ?>')">确认收货</a><?php endif; ?>
						<?php if($vo["paytype"] == 4): ?><a class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;" onclick="refund('<?php echo ($vo["id"]); ?>')">已付款</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
				<?php if(count($list) == 0): ?><tr><td colspan="10">暂无数据</td></tr><?php endif; ?>
			</table>
			<div class="pagination" style="float: right;"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/js/layer/layer.js"></script>
	<script src="/public/js/jedate/jedate.js"></script>
	<script src="/public/js/artDialog/artDialog.js"></script>
	<script type="text/javascript">
        document.getElementById('status').value='<?php echo ($status); ?>';
        document.getElementById('paytype').value='<?php echo ($paytype); ?>';
        $(function () {
            jeDate("#start_time",{
                theme:{bgcolor:"#00A680",pnColor:"#00DDAA"},
                format: "YYYY-MM-DD hh:mm:ss",
            });
            jeDate("#end_time",{
                theme:{bgcolor:"#00A680",pnColor:"#00DDAA"},
                format: "YYYY-MM-DD hh:mm:ss",
            });
        });

        //已付款
        function refund(id) {
            layer.msg('是否确认已还款？', {
                time: 0 //不自动关闭
                ,btn: ['确定', '取消']
                ,yes:function () {
                    $.ajax({
                        url:'<?php echo U("Orders/refund");?>',
                        data:{id:id,paytype:5},
                        type:'POST',
                        dataType:'json',
                        success:function (res) {
                            if (res.code == 1) {
                                // layer.alert('操作成功', {icon: 6});
                                // $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                                setTimeout("location.reload()",1000);
                            }else {
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                            }
                        }
                    })
                }
			});
        }

        //确认发货
        function confirm_deliver(id) {
            layer.msg('是否确认发货？', {
                time: 0 //不自动关闭
                ,btn: ['确定', '取消']
                ,yes:function () {
                    $.ajax({
                        url:'<?php echo U("Orders/change_status");?>',
                        data:{id:id,status:4},
                        type:'POST',
                        dataType:'json',
                        success:function (res) {
                            if (res.code == 1) {
                                // layer.alert('操作成功', {icon: 6});
                                // $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                                setTimeout("location.reload()",1000);
                            }else {
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                            }
                        }
                    })
                }
            });
        }

        //确认收货
        function confirm_take_deliver(id) {
            layer.msg('是否确认收货？', {
                time: 0 //不自动关闭
                ,btn: ['确定', '取消']
                ,yes:function () {
                    $.ajax({
                        url:'<?php echo U("Orders/change_status");?>',
                        data:{id:id,status:1},
                        type:'POST',
                        dataType:'json',
                        success:function (res) {
                            if (res.code == 1) {
                                // layer.alert('操作成功', {icon: 6});
                                // $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                                setTimeout("location.reload()",1000);
                            }else {
                                $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                            }
                        }
                    })
                }
            });
        }
	</script>
</body>
</html>