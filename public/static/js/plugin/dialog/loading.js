; (function($){
	
	window.loading = {
			
			show: function(){
				var hi = $(document).height();
				$("body").append("<div id='dialog-box-mask' style='display:block;height:"+hi+"px;'></div>" );
				$("#loading").css("top",($(window).height()-$("#loading img").height())*2/5).show();
			},
			hide: function(){
				$("#dialog-box-mask").remove();
				$("#loading").hide();
			}
			
	};
	
})(jQuery);