<?php

namespace app\common\services;

//只封装通用方法


use yii\helpers\Html;

class UtilService
{
    public static function getIP(){
    	//如果设置反向代理，则以他来获取
    	if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    		return $_SERVER['HTTP_X_FORWARDED_FOR'];
    	}
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function encode($display){
        return Html::encode($display);
    }

    public static function getRootPath(){
        return dirname( \Yii::$app->vendorPath );
    }

    public static  function isWechat(){
        $ug= isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        if( stripos($ug,'micromessenger') !== false ){
            return true;
        }
        return false;
    }
}