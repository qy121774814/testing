<?php

namespace app\modules\web\controllers;

use app\modules\web\controllers\common\BaseController;
use app\models\User;
use app\common\services\UrlService;


class UserController extends BaseController
{
        public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module,$config);
        $this->layout = "main";
    }

    //登录页面
    public function actionLogin()
    {
        //如果是get请求，直接展示登录页面
        if( \Yii::$app->request->isGet ){
            $this->layout = "user";
            return $this->render( "login" );

        }

        //登录逻辑处理
        $login_name = trim( $this->post( "login_name","" ) );
        $login_pwd = trim( $this->post( "login_pwd","" ) );
        if( !$login_name || !$login_pwd ){
            return $this->renderJs( '请输入正确的用户名和密码-1~~',UrlService::buildWebUrl('/user/login') );
        }

        //从用户表获取login_name = $login_name 是否存在
        $user_info = User::find()->where([ 'login_name' => $login_name ])->one();
        if( !$user_info ){
            return $this->renderJs( '请输入正确的用户名和密码-2~~',UrlService::buildWebUrl('/user/login') );
        }

        //验证密码
        //密码加密算法 = md5( login_pwd + md5（ login_salt ）)
       
        if ( !$user_info->verifyPassword( $login_pwd ) ) {
            return $this->renderJs( '请输入正确的用户名和密码-3~~',UrlService::buildWebUrl('/user/login') ); 
        }

        //保存用户登陆状态
        //COOKIE进行保存用户登陆状态
        //cookie加密 : 加密字符串+ "#" + uid ,加密字符串 = md5( login_name + login_pwd + login_salt )
        // $auth_token = md5( $user_info['login_name'].$user_info['login_pwd'].$user_info['login_salt'] );
        $this->setLoginStatus( $user_info );
        if ($this->setLoginStatus( $user_info )) {
            return time();exit();
        }
        return $this->redirect( UrlService::buildWebUrl("/dashboard/index") );
    }

    //编辑当前登录人的信息
        public function actionEdit()
    {
        //返回登录人信息
        if ( \Yii::$app->request->isGet ) {
            return $this->render('edit',[ 'user_info' =>$this->current_user ]);

        }

        $nickname = trim( $this->post( 'nickname',"" ) );
        $email = trim( $this->post( 'email',"" ) );
        if ( mb_strlen( $nickname,"utf-8" ) < 1 ) {
            return $this->renderJson( [],"请输入合法的姓名",-1 );
        }
        
        if ( mb_strlen( $email,"utf-8" ) < 1 ) {
            return $this->renderJson( [],"请输入合法的邮箱",-1 );
        }

        $user_info = $this->current_user;
        $user_info->nickname = $nickname;
        $user_info->email = $email;
        $user_info->updated_time = date("Y-m-d H:i:s");
        $user_info->update(0);
        return $this->renderJson( [],"编辑成功~~");


    }

    //重置当前登录的密码
        public function actionResetPwd()
    {
        if (\Yii::$app->request->isGet) {
        	return $this->render('reset_pwd',[ 'user_info' =>$this->current_user ]);
        }

        $old_password = trim( $this->post("old_password") );
        $new_password = trim( $this->post("new_password") );
        
        if ( mb_strlen( $old_password,"utf-8" ) < 1 ) {
            return $this->renderJson( [],"请输入原密码",-1 );
        }

        if ( mb_strlen( $new_password,"utf-8" ) < 1 ) {
            return $this->renderJson( [],"请输入不少于6位字符的新密码~~",-1 );
        }

        if ( $old_password == $new_password ) {
         	return $this->renderJson( [],"请重新输入一个吧，新密码和原密码不能相同哦~~",-1 );
        }

         $user_info = $this->current_user;
         

         if ( !$user_info->verifyPassword( $old_password ) ) {
         	return $this->renderJson( [],"请检查原密码是否正确~~",-1  );
         }

         $user_info->setPassword( $new_password );
         $user_info->updated_time = date("Y-m-d H:i:s");
         $user_info->update(0);

         $this->setLoginStatus( $user_info );

         return $this->renderJson( [],"重置密码成功~~" );

    }

    //退出
    public function actionLogout(){
        $this->removeLoginStatus();
        return $this->redirect( UrlService::buildWebUrl('/user/login') );
    }
} 
