(function($){

    laydate.render({
        elem:"input[name='date']"
    });

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


})(jQuery);