<?php


namespace app\modules\web\controllers;


use app\common\services\UploadService;
use app\modules\web\controllers\common\BaseController;

class UploadController extends BaseController{
    /*
     * 上传借口
     * bucket:avatar/brand/book
     * iframe：里面可以加载一个页面，子页面与父页面。子页面可以调父页面的JS
     * window.parent  :表示子页面调用父页面   upload  是对象
     * */

    private $allow_file_type = ['png','gif','jpg','jpeg'];

     public function actionPic(){
         $bucket = trim( $this->post( 'bucket','' ) );
         $callback = 'window.parent.upload';//error,success 两个方法

         if( !$_FILES || !isset( $_FILES['pic'] ) ){
             return "<script>{$callback}.error( '请选择文件之后再提交~~')</script>";

         }

         $file_name = $_FILES['pic']['name'];
         $tmp_file_extend = explode( '.',$file_name );

         if( !in_array(strtolower(end($tmp_file_extend)),$this->allow_file_type) ){
             return "<script>{$callback}.error('请上传制定类型的图片，类型允许png,gif,jpg,jpeg~~')</script>";
         }

         //上传图片的业务逻辑  todo
         $ret = UploadService::uploadByFile( $file_name , $_FILES['pic']['tmp_name'] ,$bucket );
         if( !$ret ){
             return "<script>{$callback}.error('".UploadService::getLastErrorMsg()."')</script>";
         }
         return "<script>{$callback}.success('{$ret['path']}')</script>";

     }

}