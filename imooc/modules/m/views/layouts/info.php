<!--统一布局文件-->
<?php 
use app\assets\MAsset;
MAsset::register($this);
?>
<?php $this->beginPage();?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
	<title>一猿工作室</title>
    <?php $this->head();?>
<body>
<?php $this->beginBody();?>
<!--不同部分begin-->
<?=$content?>
<!--不同部分end-->
<div class="pro_fixed clearfix">
    <a href="/m/"><i class="sto_icon"></i><span>首页</span></a>
            <a class="fav" href="javascript:void(0);" data="4"><i class="keep_icon"></i><span>收藏</span></a>
        <input type="button" value="立即订购" class="order_now_btn" data="4"/>
    <input type="button" value="加入购物车" class="add_cart_btn" data="4"/>
    <input type="hidden" name="id" value="4">
</div>
</div>
<div class="copyright clearfix">
	        <p class="name">欢迎您，郭威</p>
	    <p class="copyright">由<a href="/" target="_blank">编程浪子</a>提供技术支持</p>
</div>

<?php $this->endBody();?>
</body>
</html>
<?php $this->endPage();?>