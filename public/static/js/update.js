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

    $(".form-add [type='submit']").click(function(){

        var form = $(".form-add:first");

        var reset = function(){
            form.find("input[type='text']").val("");
            form.find("textarea").val("");
            form.find("option").attr("selected", false);
            form.find("option:first-child").attr("selected", true);
        };

        $.ajax({
            url:form.attr("action"),
            method:form.attr("method"),
            data:form.serialize(),
            dataType:"json",
            success:function(res) {
                custom.alert(res.msg);
                if(res.flag == 1){
                    $(".dialog-box").click(function(){
                        reset();
                    });
                    $(".dialog-box-mask").click(function() {
                        reset();
                    });
                }
            }
        })

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
                    $("#popDIV").append(res.content);
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

    $("#ipt-project-name").click(function(){
        if($(".f-layer-project").length>0){
            $(".flayer-project-back").show();
            $(".f-layer-project").addClass("show");
        }else{
            $.ajax({
                url:"project/searchProject.html",
                dataType:'json',
                success:function(res){
                    $("#popDIV").append(res.content);
                    $(".f-layer-project .close").click(function () {
                        $(".f-layer-project-back").hide();
                        $(".f-layer-project").removeClass("show");
                    });
                    $(".f-layer-project .save").click(function(){
                        var radio = $("#projectList .selectRadio:checked");
                        if( radio.length>0 ){
                            $("input[name='pid']").val(radio.val());
                            $("input[name='pname']").val(radio.attr('data'));
                            $(".f-layer-project .close").click();
                        }else{
                            custom.alert('请至少选择一项');
                        }

                    });
                    $(".f-layer-project-back").show();
                    setTimeout('$(".f-layer-project").addClass("show")',500);
                }
            })
        }
    });

    $("#form-company-add input[name='type']").change(function(){

        var p = $("#form-company-add [name='flag']").parents(".form-group");
        if( $(this).val() == 2 ){
            p.hide();
        }else{
            p.show();
        }

    });

    $("#inputDepartment").click(function(){
        if($(".f-layer-department").length>0) {
            $(".f-layer-department-back").show();
            $(".f-layer-department").addClass("show");
        }else {
            $.ajax({
                url: "/department/searchDepartment",
                method: "post",
                dataType: "json",
                success: function (res) {
                    $("#popDIV").append(res.content);
                    $(".f-layer-department .close").click(function () {
                        $(".f-layer-department-back").hide();
                        $(".f-layer-department").removeClass("show");
                    });
                    $(".f-layer-department .save").click(function () {
                        var radio = $(".f-layer-department .selectRadio:checked");
                        if (radio.length > 0) {
                            $("input[name='did']").val(radio.val());
                            $("input[name='department']").val(radio.attr('data'));
                            $(".f-layer-department .close").click();
                        } else {
                            custom.alert('请至少选择一项');
                        }

                    });
                    $(".f-layer-department-back").show();
                    setTimeout('$(".f-layer-department").addClass("show")', 500);
                }
            });
        }
    });


})(jQuery);