(function ($) {

    laydate.render({
        elem: "input[name='date']",
        min: '1970-01-01 08:00:00',
        max: 0
    });

    $("#form-update [type='submit']").click(function () {
        var form = $("#form-update");
        var data = new FormData(form[0]);
        form.validate({
            'submitHandler': function () {
                var form = $("#form-update");
                var data = new FormData(form[0]);
                $.ajax({
                    url: form.attr("action"),
                    method: form.attr("method"),
                    data: data,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (res) {
                        custom.alert(res.msg);
                        if(res.flag == 1) {
                            var el;
                            if ($("#ipt-attachment").length > 0) {
                                el = "#ipt-attachment";
                            }
                            $(el).val("");
                            if ( res.image !== undefined) {
                                $(el).parent().addClass("attachment").find("img").remove();
                                $(el).parent().prepend('<img src="' + res.image + '"  width="240" data-action="zoom"/>');
                            }
                        }
                    }
                });
            }
        });
    });

    $(".form-add [type='submit']").click(function () {

        var form = $(".form-add:first");
        form.validate({
            'submitHandler': function () {
                var form = $(".form-add:first");
                var data = new FormData(form[0]);
                $.ajax({
                    url: form.attr("action"),
                    method: form.attr("method"),
                    data: data,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (res) {
                        custom.alert(res.msg);
                        if (res.flag == 1) {
                            $(".dialog-box").click(function () {
                                form[0].reset();
                            });
                            $(".dialog-box-mask").click(function () {
                                form[0].reset();
                            });
                        }
                    }
                })
            }
        });

    });

    $("#ipt-company-name").click(function () {
        $(".f-layer-back").show();
        if ($(".f-layer-company").length > 0) {
            $(".f-layer-back").hide();
            $(".f-layer-company-back").remove();
            $(".f-layer-company").remove();
        }
        var type = $(this).parents("form").find('select[name="type"]').val();
        $.ajax({
            url: "/company/searchCompany?c_type="+type,
            method: "post",
            dataType: "json",
            success: function (res) {
                $(".f-layer-back").hide();
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

    });

    $("#ipt-project-name").click(function () {
        $(".f-layer-back").show();
        if ($(".f-layer-project").length > 0) {
            $(".f-layer-back").hide();
            $(".f-layer-project-back").show();
            $(".f-layer-project").addClass("show");
        } else {
            $.ajax({
                url: "project/searchProject.html",
                dataType: 'json',
                success: function (res) {
                    $(".f-layer-back").hide();
                    $("#popDIV").append(res.content);
                    $(".f-layer-project .close").click(function () {
                        $(".f-layer-project-back").hide();
                        $(".f-layer-project").removeClass("show");
                    });
                    $(".f-layer-project .save").click(function () {
                        var radio = $("#projectList .selectRadio:checked");
                        if (radio.length > 0) {
                            $("input[name='pid']").val(radio.val());
                            $("input[name='pname']").val(radio.attr('data'));
                            $(".f-layer-project .close").click();
                        } else {
                            custom.alert('请至少选择一项');
                        }

                    });
                    $(".f-layer-project-back").show();
                    setTimeout('$(".f-layer-project").addClass("show")', 500);
                }
            })
        }
    });

    $("#form-company-add input[name='type']").change(function () {

        var p = $("#form-company-add [name='flag']").parents(".form-group");
        if ($(this).val() == 2) {
            p.hide();
        } else {
            p.show();
        }

    });

    $("#inputDepartment").click(function () {
        $(".f-layer-back").show();
        if ($(".f-layer-department").length > 0) {
            $(".f-layer-back").hide();
            $(".f-layer-department-back").show();
            $(".f-layer-department").addClass("show");
        } else {
            $.ajax({
                url: "/department/searchDepartment",
                method: "post",
                dataType: "json",
                success: function (res) {
                    $(".f-layer-back").hide();
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
                            $("input[name='dname']").val(radio.attr('data'));
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

    $("#ipt-member-name").click(function () {
        $(".f-layer-back").show();
        if ($(".f-layer-member-1").length > 0) {
            custom.showFilter(".f-layer-member-1");
        } else {
            $.ajax({
                url: "/member/selectMember",
                method: "POST",
                success: function (res) {
                    $("body").append(res);
                    $(".f-layer-member-1 .btn.save").click(function () {
                        var radio = $(".f-layer-member-1 .selectRadio:checked");
                        if (radio.length > 0) {
                            $("input[name='mname']").val(radio.parent().parent().find("td:eq(1)").html());
                            $("input[name='mcode']").val(radio.parent().parent().find("td:eq(2)").html());
                            $("input[name='mid']").val(radio.val());
                            custom.hideFilter(".f-layer-member-1");
                        } else {
                            custom.alert("请至少选择一项");
                        }
                        return false;
                    });
                    custom.showFilter(".f-layer-member-1", 1);
                }
            });
        }
    });


})(jQuery);