(function ($) {

    $(".pagination a").attr("target", "_blank").click(function () {
        var url = $(this).attr("href");
        url = url.replace("#", "/");
        var data = $("#main-container .f-layer form").serialize();
        $.ajax({
            url: url,
            method: "POST",
            data: data,
            success: function (res) {
                $("#main-pannel").html(res);
                pageLimit();
            }
        });
        return false;
    });

    $("table.list th a").attr("target", "_blank").click(function () {
        var data = $("#main-container .f-layer form").serialize();
        $.ajax({
            url: $(this).attr("href"),
            method: "POST",
            data: data,
            success: function (res) {
                $(".main-pannel.all-page").html(res);
                pageLimit();
            }
        });
        return false;
    });

    $("table.list td a").attr("target", "_blank").click(function () {
        if ($(this).hasClass("export")) {
            return true;
        }else if ($(this).hasClass("deleteOrders")) {
            console.log('aaa');
            var url = $(this).attr("href");
            $.ajax({
                url: url,
                type: "json",
                success: function(data){
                    custom.alert(data.message);
                    window.setTimeout("location.reload()", 800);
                }
            });
            return false;
        }else {
            var url = $(this).attr("href");
            url = url.replace("#", "/");
            var params = {
                url: url,
                method: "POST"
            };
            contentAjax("main-container", params);
            return false;
        }
    });

    $("table.list td a.link-statistics").unbind("click").click(function(){
        var url = $(this).attr("href");
        $.get(url,function(data){
            $("#popDIV").html(data);
            $("#popDIV .f-layer .close").click(function(){
                custom.hideFilter($("#popDIV .f-layer"));
            });
            custom.showFilter($("#popDIV .f-layer"),true);
        });
        return false;
    });

    var selIDs = {

        e: "#main-container .selected-ids select",
        add: function (name, value) {
            this.remove(value);
            $(this.e).append('<option value="' + value + '" selected>' + name + '</option>');
        },
        remove: function (value) {
            $(this.e).find("option[value='" + value + "']").remove();
        },
        val: function () {
            return $(this.e).val();
        }

    };

    var allChecked = true;

    $("#main-container .lists-id").each(function () {
        if (in_array($(this).val(), selIDs.val())) {
            $(this).prop("checked", true);
        } else {
            allChecked = false
        }
    });

    if (allChecked == true && $("#main-container .lists-id").length > 0) {
        $("#main-container .checkAll").prop("checked", true);
    }

    var checkAllChecked = function () {
        var ret = true;
        $("#main-container .lists-id").each(function () {
            if ($(this).prop("checked") == false) {
                ret = false;
            }
        });
        if ($("#main-container .lists-id").length == 0) {
            ret = false;
        }
        return ret;
    };

    $("#main-container .lists-id").click(function () {
        var flag = $(this).prop("checked");
        if (flag == true) {
            selIDs.add($(this).attr("data-name"), $(this).val());
            if (checkAllChecked()) {
                $("#main-container .checkAll").prop("checked", true);
            }
        } else {
            selIDs.remove($(this).val());
            $("#main-container .checkAll").prop("checked", false);
        }
    });

    $("#main-container .checkAll").click(function () {
        var flag = $(this).prop("checked");
        $("#main-container .lists-id").prop("checked", flag);
        if (flag == true) {
            $("#main-container .lists-id").each(function () {
                selIDs.add($(this).attr("data-name"), $(this).val());
            });
        } else {
            $("#main-container .lists-id").each(function () {
                selIDs.remove($(this).val());
            });
        }
    });

    $("input[type=button].page-jump").unbind("click").click(function(){
        var page = $(this).parent().find("input.page-jump").val();
        var url = $(this).attr("data-url");
        if(url.match("[\?]")) {
            url = url + "&page=" + page;
        } else {
            url= url + "?page=" + page;
        }
        var reg = /^\d+(?=\.{0,1}\d+$|$)/;
        if(reg.test(page) && page > 0){
            $.ajax({
                url:url,
                method:"POST",
                success:function(res){
                    $("#main-pannel").html(res);
                    pageLimit();
                }
            });
        }else{
            custom.alert("页数错误");
        }
    });

    $("input[type=text].page-jump").keydown(function(e){
        if(e.keyCode==13){
            $(this).parent().find("input[type=button].page-jump").click();
        }
    });

})(jQuery);
