; if( custom ){
    console.log('WARNING: custom is defined.');
}else{

    var custom = {};

    custom.params = {
        hasClose:true,
        hasMask:true,
        maskClose:true,
        title:'新智超脑',
        content:false,
        confirmValue:'确认',
        cancelValue:'取消',
        hasBtn:false,
        time: 5000,
        autoHide: true,
        confirm:function(){}
    };

    custom.alert = function ( msg ) {
        var params = $.extend({},this.params,{content:msg});
        $("#dialog-box").dialogBox(params);
    };

    custom.confirm = function(msg,func){

        var params = $.extend({},this.params,{
            hasBtn : true,
            content: msg,
            confirm : func,
            maskClose : false
        });
        $("#dialog-box").dialogBox(params);
    };

    custom.showFilter = function(el,flag){
        elBack = $(".f-layer-back");
        if(elBack.length == 0){
            $("body").append('<div class="f-layer-back"' +
                ' style="display:none;background-color: black;opacity: 0.5;width: 100%;height: 100%;position: fixed;top: 0;z-index:999"></div>');
            elBack = $(".f-layer-back");
        }
        elBack.show();
        if( flag == 1 || flag == true ){
            // noinspection JSAnnotator
            window.fel = el;
            setTimeout("$(fel).addClass('show')",500);
        }else{
            $(el).addClass("show");
        }
    };

    custom.hideFilter = function(el){
        $(".f-layer-back").hide();
        $(el).removeClass("show");
    }

}