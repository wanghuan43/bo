/**
 * 列表页JS.
 */

//删除功能
(function ($) {

    $("#lists-delete").click(function () {

        var ids = new Array(),$this = $(this);
        $(".lists-id:checked").each(function () {
            ids.push($(this).val());
        });

        if(ids.length > 0){
            custom.confirm("确定要删除吗？",function(){
                $.ajax({
                    "url":$this.attr("href"),
                    "method":"POST",
                    "data":{"ids":ids},
                    "dataType":"json",
                    "success":function(res){
                        custom.alert(res.msg);
                        if( res.flag == 1 )
                            $(".lists-id:checked").parents("tr").remove();
                    }
                });
            });
        }else{
            custom.alert('请至少选择一项');
        }

        return false;
    });

    $("#btn-circulation").click(function(){
        var ids = new Array(),$this = $(this);
        $(".lists-id:checked").each(function () {
            ids.push($(this).val());
        });
        if(ids.length>0){
            if( $(".f-layer-member").length>0){
                $(".f-layer-member-back").show();
                $(".f-layer-member").addClass("show");
            }else {
                $.ajax({
                    url: '/member/searchMember',
                    success: function (res) {
                        $("body").append(res.content);
                        $(".f-layer-member .save").html("传阅");
                        $(".f-layer-member .close").html("关闭");
                        $(".f-layer-member-back").show();
                        $(".f-layer-member .close").click(function(){
                            $(".f-layer-member-back").hide();
                            $(".f-layer-member").removeClass("show");
                        });
                        $("#sel-ids").select2();
                        $("#checked-result").show();
                        $(".f-layer-member .save").click(function() {
                            var url = $("#btn-circulation").attr("href"), ids = new Array(), mids = new Array();
                            $(".lists-id:checked").each(function () {
                                ids.push($(this).val());
                            });
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
                                        custom.alert(res.msg);
                                    }
                                });
                            } else {
                                custom.alert("请至少选择一个用户");
                            }
                        });
                        setTimeout("$('.f-layer-member').addClass('show')", 500);
                    }
                })
            };
        }else{
            custom.alert('请至少选择一项');
        }
        return false;
    });

})(jQuery);