;
var account_index_ops = {
	init:function(){
		this.eventBind();
	},
	eventBind:function(){
		var that = this;
        $(".search").click(function(){
        	$(".wrap_search").submit();
        });

        $(".recover").click(function(){

            
            that.ops( "recover",$(this).attr("data") );
        });

        $(".remove").click(function(){

            
            that.ops( "remove",$(this).attr("data") );
        });
        
	},
	ops:function( act,uid ){
        callback = {
            'ok':function(){
                 $.ajax({ 
                   url:common_ops.buildWebUrl("/account/ops"),
                   type:'POST',
                   data:{
                     act:act,
                     uid:uid,
                   },
                   dataType:'json',
                   success:function(res){
                       var callback = null;
                       if ( res.code == 200) {
                            callback = function(){
                               window.location.href = window.location.href; 
                            }
                        }
                       common_ops.alert(res.msg,callback);
                    }
                 }); 
            },
            'cancel':function(){

            }
        },

        common_ops.confirm((act == 'remove')?"您确定删除?":"您确定恢复？",callback);
	}
};

$(document).ready(function(){
	account_index_ops.init();
})