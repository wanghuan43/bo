(function($){

    var selIDs = {

        e : "#main-container .selected-ids select",
        add : function(name,value){
            this.remove(value);
            $(this.e).append('<option value="'+value+'" selected>'+name+'</option>');
        },
        remove : function(value){
            $(this.e).find("option[value='"+value+"']").remove();
        },
        val : function(){
            return $(this.e).val();
        }

    };

    var allChecked = true;

    $("#main-container .lists-id").each(function(){
        if(in_array($(this).val(),selIDs.val())){
            $(this).prop("checked",true);
        }else{
            allChecked = false
        }
    });

    if(allChecked==true && $("#main-container .lists-id").length > 0){
        $("#main-container .checkAll").prop("checked",true);
    }

    var checkAllChecked = function(){
        var ret = true;
        $("#main-container .lists-id").each(function(){
            if($(this).prop("checked")==false){
                ret = false;
            }
        });
        if($("#main-container .lists-id").length == 0){
            ret = false;
        }
        return ret;
    };

    $("#main-container .lists-id").click(function(){
        var flag = $(this).prop("checked");
        if( flag == true ){
            selIDs.add($(this).attr("data-name"),$(this).val());
            if(checkAllChecked()){
                $("#main-container .checkAll").prop("checked",true);
            }
        }else{
            selIDs.remove($(this).val());
            $("#main-container .checkAll").prop("checked",false);
        }
    });

    $("#main-container .checkAll").click(function(){
        var flag = $(this).prop("checked");
        $("#main-container .lists-id").prop("checked",flag);
        if(flag == true){
            $("#main-container .lists-id").each(function(){
                selIDs.add($(this).attr("data-name"),$(this).val());
            });
        }else{
            $("#main-container .lists-id").each(function(){
                selIDs.remove($(this).val());
            });
        }
    });
})(jQuery);