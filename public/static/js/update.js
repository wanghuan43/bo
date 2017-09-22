(function($){


    $("#form-update [type='submit']").click(function(){
        var form = $("#form-update");
        $.ajax({
            url:form.attr("action"),
            method:form.attr("method"),
            data:form.serialize(),
            dataType:"json",
            success:function (res) {
                custom.alert(res.msg);
            }
        });
    });

    $("#ipt-company-name").click(function(){
        if($(".f-layer-company").length>0) {
            $(".f-layer-company-back").show();
            $(".f-layer-company").addClass("show");
        }else {
            $.ajax({
                url: "/company/searchCompany",
                method: "post",
                dataType: "json",
                success: function (res) {
                    $("body").append(res.content);
                    $(".f-layer-company .close").click(function () {
                        $(".f-layer-company-back").hide();
                        $(".f-layer-company").removeClass("show");
                    });
                    $(".f-layer-company .save").click(function () {
                        var radio = $("#companyList .selectRadio:checked");
                        if (radio.length > 0) {
                            $("input[name='coid']").val(radio.val());
                            $("input[name='coname']").val(radio.attr('data'));
                            $(".f-layer-company .close").click();
                        } else {
                            custom.alert('请至少选择一项');
                        }

                    });
                    $(".f-layer-company-back").show();
                    setTimeout('$(".f-layer-company").addClass("show")', 500);
                }
            });
        }
    });

    /*$("#btn-add-order").click(function(){
        if($(".f-layer-orders").length>0) {
            $(".f-layer-orders-back").show();
            $(".f-layer-orders").addClass("show");
        }else {
            $.ajax({
                url: "/orders/searchOrders",
                method: "post",
                data:{
                    'fields' : {
                        'orders' : {
                            '0' : 'o_cid',
                            '1' : 'o_money'
                        }
                    },
                    'operators' : {
                        'orders' : {
                            '0' : '=',
                            '1' : '<='
                        }
                    },
                    'values' :{
                        'orders' : {
                            '0' : new Array("0"),
                            '1' : new Array($("input[name='noused']").val())
                        }
                    }
                },
                dataType: "json",
                success: function (res) {
                    $("body").append(res.content);
                    $(".f-layer-orders .close").click(function () {
                        $(".f-layer-orders-back").hide();
                        $(".f-layer-orders").removeClass("show");
                    });
                    $(".f-layer-orders .save").click(function () {
                        var radio = $("#ordersList .selectRadio:checked");
                        if (radio.length > 0) {
                            $("input[name='coid']").val(radio.val());
                            $("input[name='coname']").val(radio.attr('data'));
                            $(".f-layer-company .close").click();
                        } else {
                            custom.alert('请至少选择一项');
                        }

                    });
                    $(".f-layer-orders-back").show();
                    setTimeout('$(".f-layer-orders").addClass("show")', 500);
                }
            });
        }
    });*/

})(jQuery);