/**
 * 列表页JS.
 */

//删除功能
(function ($) {

    $(".f-layer-member").remove();
    $(".f-layer-member-back").remove();

    $("#lists-all-checked").click(function () {
        var _this = $(this);
        $(".lists-id").prop("checked", _this.prop("checked"));
    });

    $("#link-export").click(function(){
        var type = $(this).attr("data-type");
        var action = $(this).attr("href");
        var form = $("#"+type+"AllForm");
        var values = false;

        form.find(".values").each(function(){
            var val = $(this).val();
            if(val != '' && val != 0 && val != undefined){
                values = true;
            }
        });

        var sIDs = form.find(".selected-ids select").val();

        if(sIDs.length>0){
            msg = '是否要导出选中的内容?';
        }else{
            if(values){
                msg = '是否要导出符合条件的内容?';
            }else{
                msg = '是否要导出全部内容?';
            }
        }
        custom.confirm(msg,function(){
            $("#for-export").html(form.clone());
            form = $("#for-export").find("form");
            form.attr({ 'id':type+'AllForm2',"action":action,"method":"POST"});
            form.find(".selected-ids select").attr("name","ids[]");
            form.append("<input type='hidden' name='operator' value='export'/>");
            form.submit();
        });

        return false;
    });

    $("#lists-delete").click(function () {

        var ids = new Array(), $this = $(this);
        $(".main-pannel .lists-id:checked").each(function () {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            custom.confirm("确定要删除吗？", function () {
                $.ajax({
                    "url": $this.attr("href"),
                    "method": "POST",
                    "data": {"ids": ids},
                    "dataType": "json",
                    "success": function (res) {
                        custom.alert(res.msg);

                        if(res.failed){
                            var f = res.failed;
                            var c = $("#main-container .search-panel .f-layer .selected-ids");
                            for(var i=0;i<f.length;i++){
                               var id = f[i];
                               var h = c.find("option[value="+id+"]").addClass("red").html();
                               c.find("li[title="+h+"]").addClass("red");
                            }
                        }

                        if (res.flag == 1 || res.status==1)
                            window.setTimeout("location.reload()", 500);
                    }
                });
            });
        } else {
            custom.alert('请至少选择一项');
        }

        return false;
    });

    var doCirculation = function ( e ) {
        var url = e.data.url, ids = new Array(), mids = new Array();
        ids = $("#main-container .selected-ids select").val();
        mids = $("#sel-ids").val();
        if (mids.length > 0) {
            loading.show();
            $.ajax({
                url: url,
                data: {
                    ids: ids,
                    mids: mids
                },
                method: "POST",
                dataType: "json",
                success: function (res) {
                    loading.hide();
                    if(e.data.refresh){
                        contentAjax('main-container',{url:e.data.refresh});
                    }else {
                        custom.alert(res.msg);
                    }
                }
            });
        } else {
            custom.alert("请至少选择一个用户");
        }
    };

    $("#btn-circulation").click(function () {
        var ids = new Array(), $this = $(this),url=$(this).attr("href");
        $(".lists-id:checked").each(function () {
            ids.push($(this).val());
        });
        if (ids.length > 0) {
            if ($(".f-layer-member").length > 0) {
                if( $._data($(".f-layer-member .save")[0],"events").click[0].data.url != url ){
                    $(".f-layer-member .save").unbind("click").click({url:url},doCirculation);
                }
                $(".f-layer-member-back").show();
                $(".f-layer-member").addClass("show");
            } else {
                $.ajax({
                    url: '/member/searchMember',
                    success: function (res) {
                        $("#popDIV").append(res.content);
                        $(".f-layer-member .save").html("传阅");
                        $(".f-layer-member .close").html("关闭");
                        $(".f-layer-member-back").show();
                        $(".f-layer-member .close").click(function () {
                            $(".f-layer-member-back").hide();
                            $(".f-layer-member").removeClass("show");
                        });
                        $("#sel-ids").select2();
                        $("#checked-result").show();
                        $(".f-layer-member .save").click({url:url},doCirculation);
                        setTimeout("$('.f-layer-member').addClass('show')", 500);
                    }
                })
            };
        } else {
            custom.alert('请至少选择一项');
        }
        return false;
    });

    $("#btn-add-members").click(function(){
        var url = $(this).attr("href"),type=$(this).attr("data-type"),id=$(this).attr("data-id");
        if ($(".f-layer-member").length > 0) {
            if( $._data($(".f-layer-member .save")[0],"events").click[0].data.url != url ){
                $(".f-layer-member .save").unbind("click").click({url:url},doCirculation);
            }
            $(".f-layer-member-back").show();
            $(".f-layer-member").addClass("show");
        } else {
            $.ajax({
                url: '/member/searchMember',
                success: function (res) {
                    $("#popDIV").append(res.content);
                    $(".f-layer-member .save").html("传阅");
                    $(".f-layer-member .close").html("关闭");
                    $(".f-layer-member-back").show();
                    $(".f-layer-member .close").click(function () {
                        $(".f-layer-member-back").hide();
                        $(".f-layer-member").removeClass("show");
                    });
                    $("#sel-ids").select2();
                    $("#checked-result").show();
                    $(".f-layer-member .save").click({url:url,refresh:"/circulation/list/type/"+type+"/id/"+id},doCirculation);
                    setTimeout("$('.f-layer-member').addClass('show')", 500);
                }
            })
        };
    });

    /*$("table.list tr").hover(function(){
        $(this).addClass("hover");
    },function(){
        $(this).removeClass("hover");
    });*/
    $("#searchList").click(function(){
        loading.show();
        var stype = $(this).attr("stype");
        var cla = ".f-layer-"+stype;
        var url = $(this).attr("href");
        $.ajax({
            url: url,
            dataType: "json",
            method: "post",
            success: function (data) {
                loading.hide();
                $("#popDIV").html(data.content);
                $(cla).addClass("show");
                //$(cla).show();
                $(cla + "-back").show();
                $(cla + " .close").click(function () {
                    //$(cla).hide();
                    $(cla).removeClass("show");
                    $(cla + "-back").hide();
                    return false;
                });
            }
        });
        return false;
    });

})(jQuery);