;
var user_edit_ops = {
    init:function(){
    	this.eventBind();

    },
    eventBind:function(){
    	$(".save").click( function(){

            //避免重复点击
            var btn_target = $(this);//等于当前作用域
            if (btn_target.hasClass( "disabled" )) {
                common_ops.alert( "正在处理，请不要重复点击~~" );
                return false;

            }
            //$(".user_edit_wrap input[name = nickname]")
    		var nickname = $("input[name = nickname]").val();
            var email = $("input[name = email]").val();
            if ( nickname.length < 1 ) {
            	common_ops.tip("请输入合法的姓名~~","input[name = nickname]");
            	return false;
            }

            if ( email < 1 ) {
                common_ops.tip("请输入合法的邮箱地址","input[name = email]"); 
                return false;
            }

            btn_target.addClass( "disabled" );

            $.ajax({
                url:common_ops.buildWebUrl('/user/edit'),
                type:'POST',
                data:{
                    nickname:nickname,
                    email:email,
                },
                dataType:'json',
                success:function(res){
                    btn_target.removeClass( "disabled" );
                    callback = null;
                  if ( res.code == 200 ) {
                    callback = function(){
                       window.location.href = window.location.href;
                    };
                  }
                  common_ops.alert(res.msg,callback);
                }
            });
    	} )
     

    }

};

$(document).ready(function(){
	user_edit_ops.init();
})