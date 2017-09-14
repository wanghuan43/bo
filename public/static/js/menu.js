; var Menu = {
		changeName : function(ele){
			var $this = $(ele);
			$this.parent().parent().find(".fancytree-title").html( $this.val() );
		},
		update:function(ele){
			var tr = $(ele).parent().parent();
			var url = tr.find("input[name='url']").val();
			var isShow = tr.find("input[name='is_show']:checked").val();
			if( url == undefined ){
				url = '';
			}
			if( isShow == undefined ){
				isShow = 0;
			}
			var data = {
					'id':$(ele).attr("data-id"),
					'url':url,
					'is_show':isShow,
					'list_order':tr.find("input[name='list_order']").val(),
					'name':tr.find("input[name='name']").val()
			};
			loading.show();
			$.ajax({				
				url:"/menu/update",	
				method:"POST",
				data:data,
				dataType:'json',
				success:function(res){
					loading.hide();
					var params = {
						hasClose:true,
						hasMask:true,
						title:'新智云',
						content:res.msg
					};	
					$("#dialog-box").dialogBox(params);
				}
			});			
		},
		remove:function(ele){
			
			$('#dialog-box').dialogBox({
				hasClose: true,
				hasBtn: true,
				confirmValue: '确认',
				confirm: function(){
					var $this = $(ele);
					var tr = $this.parent().parent();
					loading.show();
					$.ajax({
						url:"/menu/delete",
						method:"POST",
						data:{ id:$this.attr("data-id")},
						dataType:"json",
						success:function(res){
							loading.hide();
							var params = {
									hasClose:true,
									hasMask:true,
									title:'新智云',
									content:res.msg
								};
							$("#dialog-box").dialogBox(params);		
							if( res.flag == 1 ){
								tr.remove();
							}
						}
					});
				},
				cancelValue: '取消',
				title: '新智云',
				content: '确定要删除吗？'
			});

		},
		add:function(ele){
			var data = {
					url:'/menu/add',
					method:'get',
					data:{'pid':$(ele).attr("data-id")}
			};
			contentAjax("main-container",data);
			return false;
		},
		back:function(){
			var data = {
					url:'/menu/index',
					method:'get'
			}
			contentAjax("main-container",data);
		}
};