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
<style type="text/css">
.row-fluid{
    display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 30%; width: 40%;height: 300px;overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
}
.row-fluid2{
    display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 30%; width: 40%;height: 300px; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
}
.row-fluid3{
	display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 30%; width: 40%;height: 300px; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
}
#bg{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}
#bg2{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}
#bg3{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}
</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">会员管理</a></li>	
			<li><a href="<?php echo U('member/roleindex');?>">会员角色</a></li>	
			<li><a href="<?php echo U('member/level');?>">会员等级</a></li>	
			<li><a href="<?php echo U('member/leveldec');?>">会员等级说明</a></li>				
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Member/index');?>">	
			会员昵称/电话： 
			<input type="text" name="keyword" style="width: 150px;" value="<?php echo ((isset($formget["keyword"]) && ($formget["keyword"] !== ""))?($formget["keyword"]):''); ?>">&nbsp;&nbsp;
			<input type="submit" class="btn" style="background: #1dccaa;" value="查询" />
			<a class="btn" style="background: #1dccaa;" href="<?php echo U('Member/add');?>">新增</a>
		</form>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th style="min-width: 50px;text-align: center;">ID</th>
						<th style="min-width: 100px;text-align: center;">注册时间</th>
						<th style="min-width: 100px;text-align: center;">昵称</th>
						<th style="min-width: 100px;text-align: center;">备注</th>
						<th style="min-width: 100px;text-align: center;">电话</th>
						<th style="min-width: 80px;text-align: center;">可赊账天数</th>
						<th style="min-width: 80px;text-align: center;">角色</th>
						<th style="min-width: 80px;text-align: center;">会员等级</th>
						<th style="min-width: 100px;text-align: center;">操作</th>
					</tr>
				</thead>
				<?php if(is_array($member)): foreach($member as $key=>$vo): ?><tr>
                    <td style="text-align: center;"><b><?php echo ($vo["id"]); ?></b></td>
					<td style="text-align: center;"><?php echo (date('Y-m-d H:i',$vo["addtime"])); ?></td>
					<td style="text-align: center;"><?php if($vo['nickname'] != ''): echo ($vo["nickname"]); else: ?>--<?php endif; ?></td>
					<td style="text-align: center;color:#49339dcc;" onclick="showremark1div('<?php echo ($vo["id"]); ?>','<?php echo ($vo["remark1"]); ?>')" id="remark1_<?php echo ($vo["id"]); ?>"><?php if($vo['remark1'] != ''): echo ($vo["remark1"]); else: ?>添加备注<?php endif; ?></td>
					<td style="text-align: center;"><?php echo ($vo["mobile"]); ?></td>
					<td style="text-align: center;"><input type="text" style="width:50px;" id="credit_daystr_<?php echo ($vo["id"]); ?>" onblur="showchange('<?php echo ($vo["id"]); ?>')" value="<?php echo ($vo["credit_day"]); ?>"></td>
					<td style="text-align: center;color:#49339dcc;" onclick="showrolediv('<?php echo ($vo["id"]); ?>','<?php echo ($vo["role_id"]); ?>')" id="rolename_<?php echo ($vo["id"]); ?>"><?php echo ($vo["rolename"]); ?></td>
					<td style="text-align: center;color:#49339dcc;" onclick="showleveldiv('<?php echo ($vo["id"]); ?>','<?php echo ($vo["level_id"]); ?>')" id="levelname_<?php echo ($vo["id"]); ?>"><?php echo ($vo["levelname"]); ?></td>
					<td style="text-align: center;">
						<?php if($vo['status'] == 1): ?><a href="<?php echo U('Member/ban',array('id'=>$vo['id']));?>" class="btn btn-primary js-ajax-dialog-btn" style="padding: 2px 15px;color: white;background-color: #1dccaa;"  data-msg="确认冻结该客户吗？">冻结</a> 
						<?php else: ?>
							<a href="<?php echo U('Member/cancelban',array('id'=>$vo['id']));?>" class="btn btn-primary js-ajax-dialog-btn" style="padding: 2px 15px;color: white;background-color: #1dccaa;"  data-msg="确认恢复该客户吗？">恢复</a><?php endif; ?>
						<a href="<?php echo U('Orders/index',array('mid'=>$vo['id']));?>" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">订单</a>  						
					</td>
				</tr><?php endforeach; endif; ?>				
			</table>
			<div class="pagination" style="float: right;"><?php echo ($page); ?></div>
		</form>
		<fieldset>
				<div class="row-fluid" style="display: none">
					<div style="margin-top:20px;margin-left:40px;margin-bottom: 5px">
						<div>
							<div style="text-align: center;margin-top: 40px;font-size: 18px;">修改会员角色</div>							
							<div style="text-align: center;margin-top: 45px;">
								<select id="roleid">
									<?php if(is_array($roles)): $i = 0; $__LIST__ = $roles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["id"]); ?>"><?php echo ($val["rolename"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
							</div>
							<input type="hidden" id="goodsid" value="">
						</div>

					</div>
					<div style="text-align: center;margin-top: 75px;">
						<a href="javascript:;" class="btn btn-primary" onclick="close_div()">取消</a>&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" class="btn btn-primary" onclick="eachSelectrole()">确认</a>
					</div>
				</div>
				<div class="row-fluid2" style="display: none">
					<div style="margin-top:20px;margin-left:40px;margin-bottom: 5px">
						<div>
							<div style="text-align: center;margin-top: 40px;font-size: 18px;">修改会员等级</div>							
							<div style="text-align: center;margin-top: 45px;">
								<select id="levelid">
									<?php if(is_array($level)): $i = 0; $__LIST__ = $level;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["id"]); ?>"><?php echo ($val["levelname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
							</div>
							<input type="hidden" id="uid" value="">
						</div>
					</div>
					<div style="text-align: center;margin-top: 75px;">
						<a href="javascript:;" class="btn btn-primary" onclick="close_div2()">取消</a>&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" class="btn btn-primary" onclick="eachSelectlevel()">确认</a>
					</div>
				</div>
				<div class="row-fluid3" style="display: none">
					<div style="margin-top:20px;margin-left:40px;margin-bottom: 5px">
						<div>
							<div style="text-align: center;margin-top: 40px;font-size: 18px;">修改备注信息</div>
							<div style="text-align: center;margin-top: 45px;">
								<input type="text" id="remark1" placeholder="请输入备注信息">
							</div>
						</div>
					</div>
					<div style="text-align: center;margin-top: 75px;">
						<a href="javascript:;" class="btn btn-primary" onclick="close_div3()">取消</a>&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" class="btn btn-primary" onclick="addremark1()">确认</a>
					</div>
				</div>
		</fieldset>
	</div>
	<div id="bg" onclick="close_div()"></div>
	<div id="bg2" onclick="close_div2()"></div>
	<div id="bg3" onclick="close_div3()"></div>
	<script src="/public/js/common.js"></script>
	<script type="text/javascript">
		function showrolediv(id,role_id) {
	        $("#goodsid").val(id);
	        $("#roleid").val(role_id);
	        $("#bg").css('display','block');
	        $('.row-fluid').css('display','block');
	    }
	    function showleveldiv(id,levelid)
	    {
	    	$("#uid").val(id);
	        $("#levelid").val(levelid);
	        $("#bg2").css('display','block');
	        $('.row-fluid2').css('display','block');
	    }
        function showremark1div(id,remark)
        {
            $("#uid").val(id);
            $("#remark1").val(remark);
            $("#bg3").css('display','block');
            $('.row-fluid3').css('display','block');
        }
	    function close_div() {
	        $('.row-fluid').css('display','none');
	        $('#bg').css('display','none');
	    }
	    function close_div2() {
	        $('.row-fluid2').css('display','none');
	        $('#bg2').css('display','none');
	    }
        function close_div3() {
            $('.row-fluid3').css('display','none');
            $('#bg3').css('display','none');
        }
	    function eachSelectrole()
	    {
	    	var goodsid =$("#goodsid").val();
	    	var roleid =$("#roleid").val();
	    	$.ajax({
	    		url:"<?php echo U('Member/changerole');?>",
	    		data:{uid:goodsid,role_id:roleid},
	    		type:'post',
	    		success:function(data)
	    		{
	    			$("#rolename_"+data.uid).html(data.rolename);
	    			close_div();
	    		}
	    	})
	    }
	    function eachSelectlevel()
	    {
	    	var goodsid =$("#uid").val();
	    	var roleid =$("#levelid").val();
	    	$.ajax({
	    		url:"<?php echo U('Member/changelevel');?>",
	    		data:{uid:goodsid,level_id:roleid},
	    		type:'post',
	    		success:function(data)
	    		{
	    			$("#levelname_"+data.uid).html(data.levelname);
	    			close_div2();
	    		}
	    	})
	    }
	    function addremark1() {
            var uid =$("#uid").val();
            var remark1 = $('#remark1').val();
            $.ajax({
                url:"<?php echo U('Member/changeremark');?>",
                data:{uid:uid,remark1:remark1},
                type:'post',
                success:function(data)
                {
                    $("#remark1_"+data.uid).html(data.remark1);
                    console.log(data);
                    close_div3();
                }
            })
        }
		function showchange(uid)
		{
			var credit_day =$("#credit_daystr_"+uid).val();
			$.ajax({
				url:"<?php echo U('Member/changecreditday');?>",
				data:{uid:uid,credit_day:credit_day},
				type:'post',
				success:function(data)
				{
					if(data.status!=0)
					{
						$.dialog({id: 'popup', lock: true,icon:"warning", content: data.msg, time: 2});
					}
				}
			})
			
		}
	</script>
</body>
</html>