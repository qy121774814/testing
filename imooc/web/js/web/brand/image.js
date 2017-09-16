;
upload = {
    error:function( msg ){
        common_ops.alert( msg );
    },
    success:function( image_key ){
        //拼装一个HTML  问题：如何实现把前面的图片替换掉，只允许一张出现
        if(!image_key){
            return false;
        }
        var html = ' <img src="'+common_ops.buildPicUrl("brand",image_key) +'">' +
            ' <span class="fa fa-times-circle del del_image" data="'+image_key +'"></span>';
        if( $('.upload_pic_wrap .pic-each').size() > 0 ){
            $('.upload_pic_wrap .pic-each').html(html);
        }else {
            $('.upload_pic_wrap').append('<span class="pic-each"> '+ html + '</span> ');
        }
        brand_image_ops.delete_img();//没有被绑定，新图片无法删除的  ？？
    }
};

var brand_image_ops ={
    init:function(){
        this.eventBind();
        this.delete_img();
    },
    eventBind:function (){
        $(".set_pic").click(function(){
            $('#brand_image_wrap').modal('show');
        });

        $("#brand_image_wrap .upload_pic_wrap input[name=pic]").change(function(){
            $("#brand_image_wrap .upload_pic_wrap").submit();
        });

        $('#brand_image_wrap .save').click(function(){

            var btn_taget = $(this);
            if ( btn_taget.hasClass( 'disabled' ) ){
                common_ops.alert( '正在处理，请不要重复提交~~' )
                return;
            }

            var image_key = $('#brand_image_wrap .del_image').attr( 'data' );

            if( $('.upload_pic_wrap .pic-each').size() < 0 ){
                common_ops.alert( '请上传品牌LOGO' )
            }
            btn_taget.addClass('disabled');

            $.ajax({
                url:common_ops.buildWebUrl('/brand/set-image'),
                type:'POST',
                data:{
                    image_key:image_key,
                },
                dataType:'json',
                success:function(res){
                    btn_taget.removeClass('disabled');
                    var callback = null;
                    if ( res.code == 200 ){
                        callback = function(){
                            window.location.href = common_ops.buildWebUrl('/brand/images');
                        };

                    }
                    common_ops.alert(res.msg ,callback);
                }
            })

        });
        $("#brand_image_wrap  .upload_pic_wrap input[name=pic]").change(function(){

            $("#brand_image_wrap .upload_pic_wrap").submit();
        });

        $(".remove").click( function(){
            var id = $(this).attr("data");
            var callback = {
                'ok':function(){
                    $.ajax({
                        url:common_ops.buildWebUrl("/brand/image-ops"),
                        type:'POST',
                        data:{
                            id:id
                        },
                        dataType:'json',
                        success:function( res ){
                            var callback = null;
                            if( res.code == 200 ){
                                callback = function(){
                                    window.location.href = window.location.href;
                                }
                            }
                            common_ops.alert( res.msg,callback );
                        }
                    });
                },
                'cancel':null
            };
            common_ops.confirm( "确定删除？",callback );
        });
    },



    delete_img:function () {
        //用unbind 理由： 重复性绑定时候，按钮会执行好几次？？
        $('#brand_image_wrap .del_image').unbind().click(function () {
            $(this).parent().remove();//将父类删除
        });
    }
};



$(document).ready(function(){
        brand_image_ops.init();
});