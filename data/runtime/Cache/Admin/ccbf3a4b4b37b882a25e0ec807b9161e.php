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
li{margin-bottom:5px;}
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
								<div class="control-group" style="margin-top:20px;">
									<label class="control-label">商品名称：</label>
									<div class="controls">
										<input type="text" name="goodsname" maxlength="20" value="<?php echo ($goods["goodsname"]); ?>" placeholder="名称限制20字以内">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">商品描述：</label>
									<div class="controls">
										<textarea  name="goodsbrief" maxlength="200" style="width: 342px;"><?php echo ($goods["goodsbrief"]); ?></textarea>
										
									</div>
								</div>	
								<div class="control-group">
									<label class="control-label">缩略图：</label>
									<div class="controls">
										<input type="hidden" name="thumb_img" id="thumb" value="<?php echo ($goods["thumb_img"]); ?>">
										<a href="javascript:upload_one_image('图片上传','#thumb');">	
											<?php if($goods['thumb_img'] != ''): ?><img src="<?php echo ($goods["thumb_img2"]); ?>" id="thumb-preview" width="100" height="100" style="cursor: hand" />
											<?php else: ?>						
											<img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb-preview" width="100" height="100" style="cursor: hand" /><?php endif; ?>
										</a>
										<input type="button" class="btn btn-small" onclick="$('#thumb-preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb').val('');return false;" value="取消图片">
									</div><br/>
									<div style="margin-left: 180px;color: red;">建议图片尺寸：300*300</div>
								</div>

								<div class="control-group">
									<label class="control-label">商品图片：</label>
									<div class="controls">
										<ul id="photos" class="pic-list unstyled"></ul>
										<?php if(!empty($imgs['photo'])): if(is_array($imgs['photo'])): foreach($imgs['photo'] as $key=>$vo): $img_url='http://'.$_SERVER['HTTP_HOST'].'/'.$vo['url']; ?>
											<li id="savedimage<?php echo ($key); ?>">
												<input id="photo-<?php echo ($key); ?>" type="hidden" name="photos_url[]" value="<?php echo ($vo['url']); ?>"> 
												<input id="photo-<?php echo ($key); ?>-name" type="text" name="photos_alt[]" value="<?php echo ($vo["alt"]); ?>" style="width: 200px;" title="图片名称">
												<img id="photo-<?php echo ($key); ?>-preview" src="<?php echo ($vo['url']); ?>" style="height:36px;width: 36px;" onclick="parent.image_preview_dialog(this.src);">
												<a href="javascript:upload_one_image('图片上传','#photo-<?php echo ($key); ?>');">替换</a>
												<a href="javascript:(function(){ $('#savedimage<?php echo ($key); ?>').remove();})();">移除</a>
											</li><?php endforeach; endif; endif; ?>
										<a href="javascript:upload_multi_image('图片上传','#photos','photos-item-wrapper');" class="btn btn-small">选择图片</a>
									</div><br/>
									<div style="margin-left: 180px;color: red;">建议图片尺寸：750*750,最多上传5张图片</div>
								</div>
								<div class="control-group">
									<label class="control-label">商品分类：</label>
									<div class="controls">
										<?php if(is_array($catlist)): $key = 0; $__LIST__ = $catlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($key % 2 );++$key;?><span style="padding: 5px 5px;"><input type="checkbox" style="margin-top: -3px;" name="catids[]" value="<?php echo ($va["id"]); ?>" <?php if($va['isdo'] == 1): ?>checked<?php endif; ?>><?php echo ($va["catname"]); ?></span><?php if(($key+1)%8 == 1): ?><br/><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</div>
								</div>				
								<div class="control-group">
									<label class="control-label">排序：</label>
									<div class="controls">
										<input type="text" name="sortorder" maxlength="3" value="<?php echo ($goods["sortorder"]); ?>" placeholder="请输入排序序号，序号越小越排前面">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">推荐：</label>
									<div class="controls">
										<input type="radio" name="isindex" value="1" <?php if($goods['isindex'] == 1): ?>checked<?php endif; ?>> 是&nbsp;&nbsp;&nbsp;
										<input type="radio" name="isindex" value="0" <?php if($goods['isindex'] == 0): ?>checked<?php endif; ?>> 否&nbsp;
									</div>
								</div>
								<div class="control-group">
									
									<label class="control-label">价格/单位：</label>
									<div class="controls">
										现价&nbsp;<input type="number" name="price" style="width:120px;height: 34px;" value="<?php echo ($goods["price"]); ?>">&nbsp;
										成本价&nbsp;<input type="number" name="settleprice" style="width:120px;height: 34px;" value="<?php echo ($goods["settleprice"]); ?>">&nbsp;
										/&nbsp;<input type="text" style="width:100px;" name="unit" placeholder='例如：米' value="<?php echo ($goods["unit"]); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">销量：</label>
									<div class="controls">
										<input type="number" name="number" min="0" placeholder="请输入销量" value="<?php echo ($goods["number"]); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">库存：</label>
									<div class="controls">
										<?php if($option == ''): ?><input type="number" name="inventory" min="0" placeholder="请输入库存" value="<?php echo ($goods["inventory"]); ?>">
											<?php else: ?>
												<input readonly="readonly" disabled="disabled" type="number" name="inventory" min="0" placeholder="请输入库存" value="<?php echo ($goods["inventory"]); ?>"><?php endif; ?>
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
	                        	<div class="control-group" style="margin-top:20px;">
									<label class="control-label">详情：</label>									
									<div class="controls" style="width: 900px;">
										<script type="text/plain"  id="content" name="goodsdesc"><?php echo ($goods["goodsdesc"]); ?></script>
									</div>
									
								</div>
	                        </fieldset>
	                    </div>					
					 </div>
	            </div>	
				<div class="form-actions">
					<input type="hidden" name="id" value="<?php echo ($goods["id"]); ?>">
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
	<script type="text/javascript">
		 var new_specs=JSON.parse(JSON.stringify(<?php echo ($specs); ?>));
   		 var good_option=JSON.parse(JSON.stringify(<?php echo ($goods_option); ?>));
    	 console.log(good_option);
    	$(function () {
	        auto_refresh(new_specs,'1');
	    });
	    function auto_refresh(specs,ch_area) 
	    {

	        var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
	        var len = specs.length;
	        var newlen = 1;
	        var h = new Array(len);
	        var rowspans = new Array(len);
	        for(var i=0;i<len;i++){

	            html+="<th>" + specs[i].title + "</th>";
	            var itemlen = specs[i].items.length;
	            if(itemlen<=0) { itemlen = 1 };
	            newlen*=itemlen;

	            h[i] = new Array(newlen);
	            for(var j=0;j<newlen;j++){
	                h[i][j] = new Array();
	            }
	            var l = specs[i].items.length;
	            rowspans[i] = 1;
	            for(j=i+1;j<len;j++){
	                rowspans[i]*= specs[j].items.length;
	            }
	        }

	        html += '<th>现价</th>';  
	        html += '<th>成本价</th>';
	        html += '<th>单位</th>';
	        html += '<th>库存</th>';
	        html+='</tr></thead>';

	        for(var m=0;m<len;m++){
	            var k = 0,kid = 0,n=0;
	            for(var j=0;j<newlen;j++){
	                var rowspan = rowspans[m];
	                if( j % rowspan==0){
	                    h[m][j]={title: specs[m].items[kid].title,html: "<td class='full' rowspan='" +rowspan + "'>"+ specs[m].items[kid].title+"</td>\r\n",id: specs[m].items[kid].id};
	                }
	                else{
	                    h[m][j]={title:specs[m].items[kid].title, html: "",id: specs[m].items[kid].id};
	                }
	                n++;
	                if(n==rowspan){
	                    kid++; if(kid>specs[m].items.length-1) { kid=0; }
	                    n=0;
	                }
	            }
	        }

	        var hh = "";
	        var area2price='';
	        for(var i=0;i<newlen;i++){
	            hh+="<tr>";
	            var ids = [];
	            var titles = [];

	            for(var j=0;j<len;j++){
	                hh+=h[j][i].html;

	                ids.push( h[j][i].id);
	                titles.push( h[j][i].title);
	            }
	            ids =ids.join('_');
	            titles= titles.join('+');
	            var val ={ id : good_option[i]['id'],title:titles, dropprice : good_option[i]['sale_price'],saleprice : good_option[i]['sale_price'],settleprice : good_option[i]['settleprice'],weight:good_option[i]['unit'],inventory:good_option[i]['inventory']};
	            if( $(".option_id_" + ids).length>0){
	                val ={
	                    id : $(".option_id_" + ids+":eq(0)").val(),
	                    title: titles,
	                    dropprice : $(".option_dropprice_" + ids+":eq(0)").val(),
	                    saleprice : $(".option_saleprice_" + ids+":eq(0)").val(),
	                    settleprice : $(".option_settleprice_" + ids +":eq(0)").val(),
	                    inventory : $(".option_inventory_" + ids +":eq(0)").val(),
	                    weight : $(".option_weight_" + ids+":eq(0)").val(),
	                }
	            }
	            // console.log(val);
	            hh += '<td>'
	            hh += '<input  data-title="'+titles+'" data-name="option_dropprice_' + ids +'" type="text" name="option_dropprice_'+ids+'[]" class="form-control option_dropprice option_dropprice_' + ids +'" value="' +(val.dropprice=='undefined'?'':val.dropprice )+'"/></td>';
	            hh += '<input data-name="option_id_' + ids+'" type="hidden" class="form-control option_id option_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
	            hh += '<input data-name="option_ids" type="hidden" class="form-control option_ids option_ids_' + ids +'" value="' + ids +'"/>';
	            hh += '<input data-name="option_title_' + ids +'" type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
	            hh += '</td>';
	            
	            hh += '<td><input  data-name="option_settleprice_' + ids+'" type="text" name="option_settleprice_'+ids+'[]" class="form-control option_settleprice option_settleprice_' + ids +'" value="' +(val.settleprice=='undefined'?'':val.settleprice )+'"/></td>';
	            hh += '<td><input  data-name="option_weight_' + ids+'" type="text" name="option_weight_'+ids+'[]" class="form-control option_weight option_weight_' + ids +'" value="' +(val.weight=='undefined'?'':val.weight )+'"/></td>';
	             hh += '<td><input  data-name="option_inventory_' + ids+'" type="text" name="option_inventory_'+ids+'[]" class="form-control option_inventory option_inventory_' + ids +'" value="' +(val.inventory=='undefined'?'':val.inventory)+'"/></td>';
	            hh += "</tr>";
	        }
	        html+=hh;
	        html+="</table>";
	        $("#options").html(html);
   		 }
	</script>
	<script>
		var app = new Vue({
			el:"#app",
			data:{
				info:{},	
				searchCon:{
	                id:"<?php echo I('get.id');?>"	                
            	}			
			},
			created:function () {
				this.mySearch(true);
			},
			methods:{
				mySearch: function (search) {
					var data = this.searchCon;
	                $.ajax({
	                    url:"<?php echo U('edit');?>",
	                    data:data,
	                    type:"POST",
	                    dataType:"json",
	                    success: function (res) {
	                        if (res.status == 1) {
	                            $('#specs').append(res.tpl_str);
	                        }
	                    },
	                    error: function () {
	                        $.dialog({id: 'popup', lock: true,icon:"warning", content: "请求失败,请重试", time: 2});

	                    }
	                })
	            },
				add:function () {	
					optionArray();    
				    var tagvals=$('#tagforms').serialize();				
					$.ajax({
						url:'<?php echo U("Admin/goods/edit_post");?>',
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