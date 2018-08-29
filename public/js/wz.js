/**
 * Created by Dell on 2017/9/4.
 */

$(function(){

    $(".spec_item_thumb").find('i').click(function(){
        var group  =$(this).parent();
       // group.find('img').attr('src',"../addons/ewei_shopv2/static/images/nopic100.jpg");
        group.find(':hidden').val('');
        $(this).hide();
        group.find('img').popover('destroy');
    });
    $("#hasoption").click(function(){
        var obj = $(this);
        if (obj.get(0).checked){
            refreshOptions();
            $('.hasoption').attr('readonly',true);
            $("#tboption").show();
            $("#tbdiscount").show();
            $("#isdiscount_discounts").show();
            $("#isdiscount_discounts_default").hide();
            $("#commission").show();
            $("#commission_default").hide();
            $("#discounts_type1").show().parent().show();
        }else{
            $("#tboption").hide();
            refreshOptions();

            $("#isdiscount_discounts").hide();
            var isdiscount_discounts = $("#isdiscount_discounts").html();
            $("#isdiscount_discounts").html('');
            isdiscount_change();
            $("#isdiscount_discounts").html(isdiscount_discounts);

            $("#commission").hide();
            var commission = $("#commission").html();
            $("#commission").html('');
           // commission_change();
            $("#commission").html(commission);

            $("#tbdiscount").hide();
            $("#isdiscount_discounts_default").show();

            $("#commission_default").show();
            $('.hasoption').removeAttr('readonly');

            $("#discounts_type1").hide().parent().hide();
            $("#discounts_type0").click();
        }
    });
});

function addSpec(){
    var len = $(".spec_item").length;
    if( len>=3){
        $.dialog({id: 'popup', lock: true,icon:"warning", content: "规格不能超过3个", time: 2});
        return;
    }

    $("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
    var url = "./index.php?g=Admin&m=Goods&a=add_spec_tpl";
    $.ajax({
        "url": url,
        success:function(data){
            $("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary"); ;
            $('#specs').append(data.data);
            var len = $(".add-specitem").length -1;
            $(".add-specitem:eq(" +len+ ")").focus();
            //refreshOptions();
        }
    });
}
function removeSpec(specid){

    $("#spec_" + specid).remove();
    refreshOptions();
}
function addSpecItem(specid){
    $("#add-specitem-" + specid).html("正在处理...").attr("disabled", "true");
    var url = "./index.php?g=Admin&m=Goods&a=add_spec_item_tpl&specid="+specid;
    $.ajax({
        "url": url,
        success:function(data){
            $("#add-specitem-" + specid).html('<i class="fa fa-plus"></i> 添加规格项').removeAttr("disabled");
            $('#spec_item_' + specid).append(data.data);
            var len = $("#spec_" + specid + " .spec_item_title").length -1;
            $("#spec_" + specid + " .spec_item_title:eq(" +len+ ")").focus();
            //refreshOptions

        }
    });
}
function removeSpecItem(obj){
    $(obj).closest('.spec_item_item').remove();
    refreshOptions();
}

function refreshOptions(){
    var ch_area=1;

    var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
    var specs = [];
    if($('.spec_item').length<=0){
        $("#options").html('');
        return;
    }
    $(".spec_item").each(function(i){
        var _this = $(this);

        var spec = {
            id: _this.find(".spec_id").val(),
            title: _this.find(".spec_title").val()
        };

        var items = [];
        _this.find(".spec_item_item").each(function(){
            var __this = $(this);
            var item = {
                id: __this.find(".spec_item_id").val(),
                title: __this.find(".spec_item_title").val(),
                show:__this.find(".spec_item_show").get(0).checked?"1":"0"
            }
            items.push(item);
        });
        spec.items = items;
        specs.push(spec);

    });
    specs.sort(function(x,y){
        if (x.items.length > y.items.length){
            return 1;
        }
        if (x.items.length < y.items.length) {
            return -1;
        }
    });
    console.log(specs);
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
        var val ={ id : "",title:titles, dropprice : "",saleprice : "",settleprice : "",weight:"",inventory:""};
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
         console.log(val);
        hh += '<td>'
        hh += '<input data-title="'+titles+'"  data-name="option_dropprice_' + ids +'" type="text" class="form-control option_dropprice option_dropprice_' + ids +'" value="' +(val.dropprice=='undefined'?'':val.dropprice )+'"/></td>';
        hh += '<input data-name="option_id_' + ids+'" type="hidden" class="form-control option_id option_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
        hh += '<input data-name="option_ids" type="hidden" class="form-control option_ids option_ids_' + ids +'" value="' + ids +'"/>';
        hh += '<input data-name="option_title_' + ids +'" type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
        hh += '</td>';
        hh += '<td><input data-name="option_settleprice_' + ids+'" type="text" class="form-control option_settleprice option_settleprice_' + ids +'" value="' +(val.settleprice=='undefined'?'':val.settleprice )+'"/></td>';
        hh += '<td><input data-name="option_weight_' + ids+'" type="text" class="form-control option_weight option_weight_' + ids +'" value="' +(val.weight=='undefined'?'':val.weight )+'"/></td>';
        hh += '<td><input data-name="option_inventory_' + ids+'" type="text" class="form-control option_inventory option_inventory_' + ids +'" value="' +(val.inventory=='undefined'?'':val.inventory )+'"/></td>';
        hh += "</tr>";
    }
    html+=hh;
    html+="</table>";
    $("#options").html(html);

}


function optionArray()
{
    var option_dropprice = new Array();
    $('.option_dropprice').each(function (index,item) {
        option_dropprice.push($(item).val());
    });

    var option_id = new Array();
    $('.option_id').each(function (index,item) {
        option_id.push($(item).val());
    });

    var option_ids = new Array();
    $('.option_ids').each(function (index,item) {
        option_ids.push($(item).val());
    });

    var option_title = new Array();
    $('.option_title').each(function (index,item) {
        option_title.push($(item).val());
    });
    var option_settleprice = new Array();
    $('.option_settleprice').each(function (index,item) {
        option_settleprice.push($(item).val());
    });
    var option_weight = new Array();
    $('.option_weight').each(function (index,item) {
        option_weight.push($(item).val());
    });
    var option_inventory = new Array();
    $('.option_inventory').each(function (index,item) {
        option_inventory.push($(item).val());
    });
    var options = {
        option_dropprice : option_dropprice,
        option_id : option_id,
        option_ids : option_ids,
        option_title : option_title,       
        option_settleprice:option_settleprice,
        option_weight:option_weight,
        option_inventory:option_inventory   
    };

    $("#optionArray").val(JSON.stringify(options));
}



