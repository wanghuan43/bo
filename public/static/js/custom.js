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

}