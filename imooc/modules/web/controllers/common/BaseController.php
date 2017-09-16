<?php

namespace app\modules\web\controllers\common;

use app\common\components\BaseWebController;
use app\models\User;
use app\models\Member;
use app\common\services\UrlService;
use app\common\services\applog\AppLogService;
use app\common\services\ConstantMapService;


//web统一控制器当中会有一些WEB独有的验证
//指定特定的布局文件
//进行登陆验证
class BaseController extends BaseWebController
{
	protected $auth_cookie_name = "13631262490";
    protected $page_size = 50;
    public $current_user = null ;

    public $allowAllAction = [
        'web/user/login'
    ];

    public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module,$config);
        $this->layout = "main";
    }

    //登陆统一验证
    public function beforeAction( $action ){
    	// 验证是否登录
    	$is_login = $this->checkLoginStatus();
    
        if ( in_array( $action->getUniqueId(), $this->allowAllAction ) ) {
        	return true;
        }

        if ( !$is_login ) {
        	if ( \Yii::$app->request->isAjax ){
        		$this->renderJson( [],"未登录，请先登录~~~",-302 );
        	}else{
        		$this->redirect( UrlService::buildWebUrl( "/user/login" ) );
        	}
        	return false;
        }

        //记录所有用户的访问时间
        AppLogService::addAppAccessLog( $this->current_user['uid'] );

         return true;
    }
    

    //验证是否当前登录有效
    private function checkLoginStatus(){
    	$auth_cookie = $this->getCookie( $this->auth_cookie_name,'' );
    	if( !$auth_cookie ) {
    		return false;
    	}

        list( $auth_token,$uid ) = explode("#", $auth_cookie);
        if ( !$auth_token || !$uid ) {
        	return false;
        }

        if ( !preg_match("/^\d+$/", $uid) ) {
        	return false;
        }

        $user_info = User::find()->where([ 'uid' => $uid ])->one();
        if ( !$user_info ) {
        	return false;
        }


        if ( $auth_token != $this->geneAuthToken( $user_info )) {
        	return false;
        }
        $this->current_user = $user_info;
        $this->view->params['current'] = $user_info;
        return true;
    }

    //设置登陆态方法
    public function setLoginStatus( $user_info ){
        $auth_token = $this->geneAuthToken( $user_info );
        $this->setCookie( $this->auth_cookie_name,$auth_token.'#'.$user_info['uid'] );
    }

    //删除登陆状态
    public function removeLoginStatus(){
    	$this->removeCookie( $this->auth_cookie_name );
    }

    //统一生成加密字段,加密字符串 = md5()
    public function geneAuthToken( $user_info ){
    	return md5( $user_info['login_name'].$user_info['login_pwd'].$user_info['login_salt'] );
    }

//    //搜索和分页方法
//    public function setPage($query,$uid){
//
//        $status = intval( $this->get( 'status',ConstantMapService::$status_default ) );
//        $mix_kw = trim( $this->get( 'mix_kw','' ) );
//        $p = intval( $this->get('p',1) );
//
//        if ($status > ConstantMapService::$status_default) {
//            $query->andWhere( [ 'status' => $status ] );
//        }
//
//        if ($mix_kw) {
//            $where_nickname =[ 'LIKE','nickname','%'.strtr( $mix_kw,[ '%' =>'\%' , '_' => '\_' , '\\' => '\\\\' ] ).'%',false ];
//            $where_mobile =[ 'LIKE','mobile','%'.strtr( $mix_kw,[ '%' =>'\%' , '_' => '\_' , '\\' => '\\\\' ] ).'%',false ];
//
//            $query->andWhere( [ 'OR',$where_nickname, $where_mobile] );
//        }
//
//        //分页功能，需要两个参数，1.符合条件的总记录数量 2.每页显示的数量
//        $page_size = 2;
//        $total_res_count = $query->count();
//        $total_page = ceil( $total_res_count / $page_size );
//
//        $list = $query->orderBy([ "$uid" => SORT_DESC ])
//            ->offset(($p - 1) * $page_size)
//            ->limit($page_size)
//            ->all();
//
//
//
//        return [
//            'list' => $list,
//            'status_mapping' => ConstantMapService::$status_mapping,
//            'search_conditions' => [
//                'mix_kw' => $mix_kw,
//                'status' => $status,
//                'p' => $p,
//            ],
//            'pages' => [
//                'total_count' => $total_res_count,
//                'page_size' => $page_size,
//                'total_page' => $total_page,
//                'p' => $p,
//            ],
//        ];


//    }

 }



