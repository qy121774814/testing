;
upload = {
    error:function( msg ){
        common_ops.alert( msg );
    },
    success:function( image_key ){
        //拼装一个HTML  问题：如何实现把前面的图片替换掉，只允许一张出现
        if(!image_key){
            common_ops.alert( '呵呵~~' );
            return false;
        }
        var html = ' <img src="'+common_ops.buildPicUrl("brand",image_key) +'">' +
            ' <span class="fa fa-times-circle del del_image" data="'+image_key +'"></span>';
        if( $('.upload_pic_wrap .pic-each').size() > 0 ){
            $('.upload_pic_wrap .pic-each').html(html);
        }else {
            $('.upload_pic_wrap').append('<span class="pic-each"> '+ html + '</span> ');
        }
        brand_set_ops.delete_img();//没有被绑定，新图片无法删除的  ？？
    }
};

var brand_set_ops ={
    init:function(){
        this.eventBind();
        this.delete_img();
    },
    eventBind:function(){
        $('.wrap_brand_set .save').click(function(){

            var btn_taget = $(this);
            if ( btn_taget.hasClass( 'disabled' ) ){
                common_ops.alert( '正在处理，请不要重复提交~~' )
                return;
            }

            var image_key = $('.wrap_brand_set .pic-each .del_image').attr( 'data' );

            var name_taget = $('.wrap_brand_set input[name=name]');
            var name = name_taget.val();
            var mobile_taget = $('.wrap_brand_set input[name=mobile]');
            var mobile = mobile_taget.val();
            var address_taget = $('.wrap_brand_set input[name=address]');
            var address = address_taget.val();
            var description_taget = $('.wrap_brand_set textarea[name=description]');
            var description = description_taget.val();

            if( $('.upload_pic_wrap .pic-each').size() < 0 ){
                common_ops.alert( '请上传品牌LOGO' )
            }

            if ( name.length < 1 ){
                common_ops.tip( '请输入符合规范的品牌名称~~',name_taget );
                return;
            }

            if ( mobile.length < 1 ){
                common_ops.tip( '请输入符合规范的手机号码~~',mobile_taget );
                return;
            }

            if ( address.length < 1 ){
                common_ops.tip( '请输入符合规范的地址~~',address_taget );
                return;
            }

            if ( description.length < 1 ){
                common_ops.tip( '请输入符合规范的品牌介绍~~',description_taget );
                return;
            }

            btn_taget.addClass('disabled');
            var data = {
                name:name,
                image_key:image_key,
                mobile:mobile,
                address:address,
                description:description,
            };

            $.ajax({
                url:common_ops.buildWebUrl('/brand/set'),
                type:'POST',
                data:data,
                dataType:'json',
                success:function(res){
                    btn_taget.removeClass('disabled');
                    var callback = null;
                    if ( res.code == 200 ){
                        callback = function(){
                            window.location.href = common_ops.buildWebUrl('/brand/info');
                        };

                    }
                    common_ops.alert(res.msg ,callback);
                }
            })

        });
        $(".wrap_brand_set  .upload_pic_wrap input[name=pic]").change(function(){

            $(".wrap_brand_set .upload_pic_wrap").submit();
        });
    },

    delete_img:function (){
        //用unbind 理由： 重复性绑定时候，按钮会执行好几次？？
        $( '.wrap_brand_set .del_image' ).unbind().click(function(){
            $(this).parent().remove();//将父类删除
        });
    }
};
$(document).ready(function(){
    brand_set_ops.init();
});