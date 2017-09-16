<?php

namespace app\modules\web\controllers;

use app\common\services\ConstantMapService;
use app\common\services\UrlService;
use app\common\services\UtilService;
use app\models\members\Member;
use app\modules\web\controllers\common\BaseController;

class MemberController extends BaseController
{
        public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module,$config);
        $this->layout = "main";
    }
    //会员列表
    public function actionIndex()
    {
        $mix_kw = trim( $this->get("mix_kw","" ) );
        $status = intval( $this->get("status",ConstantMapService::$status_default ) );
        $p = intval( $this->get("p",1) );
        $p = ( $p > 0 )?$p:1;

        $query = Member::find();

        if( $mix_kw ){
            $where_nickname = [ 'LIKE','nickname','%'.strtr($mix_kw,['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']).'%', false ];
            $where_mobile = [ 'LIKE','mobile','%'.strtr($mix_kw,['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']).'%', false ];
            $query->andWhere([ 'OR',$where_nickname,$where_mobile ]);
        }

        if( $status > ConstantMapService::$status_default ){
            $query->andWhere([ 'status' => $status ]);
        }


        //分页功能,需要两个参数，1：符合条件的总记录数量  2：每页展示的数量
        //60,60 ~ 11,10 - 1
        $page_size = 2;
        $total_res_count = $query->count();
        $total_page = ceil( $total_res_count / $page_size );

        $list = $query->orderBy([ 'id' => SORT_DESC ])
            ->offset( ( $p - 1 ) * $page_size )
            ->limit($page_size)
            ->all( );

        $data = [];

        if( $list ){
            foreach( $list as $_item ){
                $data[] = [
                    'id' => $_item['id'],
                    'nickname' => UtilService::encode( $_item['nickname'] ),
                    'mobile' => UtilService::encode( $_item['mobile'] ),
                    'sex_desc' => ConstantMapService::$sex_mapping[ $_item['sex'] ],
                    'avatar' => UrlService::buildPicUrl( "avatar",$_item['avatar'] ),
                    'status_desc' => ConstantMapService::$status_mapping[ $_item['status'] ],
                    'status' => $_item['status'],
                ];
            }
        }

        return $this->render('index',[
            'list' => $data,
            'search_conditions' => [
                'mix_kw' => $mix_kw,
                'p' => $p,
                'status' => $status
            ],
            'status_mapping' => ConstantMapService::$status_mapping,
            'pages' => [
                'total_count' => $total_res_count,
                'page_size' => $page_size,
                'total_page' => $total_page,
                'p' => $p
            ]
        ]);
    }

    //添加或者编辑会员
        public function actionSet()
    {
        if (\Yii::$app->request->isGet) {
            $id = intval( $this->get('id',0) );
            $info = [];
            if ($id) {
                $info = Member::find()->where([ 'id' => $id])->one();
            }
            return $this->render( "set", ['info' => $info]);
        }

        $id = intval( $this->post( 'id',0 ) );
        $nickname = trim( $this->post( 'nickname','' ) );
        $mobile = trim( $this->post( 'mobile','' ) );
        $date_now = date( 'Y-m-d H:i:s' );


        if (mb_strlen( $nickname,'utf-8' ) < 1) {
            return $this->renderJson( [],'请输入符合规范的姓名~~',-1 );
        }

        if (mb_strlen( $mobile,'utf-8' ) < 1) {
            return $this->renderJson( [],'请输入符合规范的手机号码~~',-1 );
        }

        $has_in = Member::find()->where(['nickname' => $nickname])->andwhere([ '!=','id',$id ])->count();
        if ($has_in) {
            return $this->renderJson([],'该登陆名已存在，请换一个试试~~',-1);
        }

        $info = Member::find()->where( ['id' => $id] )->one();
        if ($info) {//编辑
            $model_member = $info;
        }else{//添加
            $model_member = new Member();
//            $model_member->status = 1;
            $model_member->avatar = ConstantMapService::$default_avatar;
            $model_member->created_time = $date_now;
        }

        $model_member->nickname = $nickname;
        $model_member->mobile = $mobile;
        $model_member->updated_time = $date_now;
        $model_member->save( 0 );
        return $this->renderJson( [],'操作成功~~' );
    }
    //会员详情
    public function actionInfo()
    {

        return $this->render( "info" );
    }
    //会员评论
    public function actionComment()
    {

        return $this->render( "comment" );
    }

    public function actionOps(){
        if (!\Yii::$app->request->isPost ){
            return $this->renderJson( [],'系统繁忙，请稍后再式~~',-1  );
        }

        $id = intval( $this->post( 'id',0 ) );
        $act = trim( $this->post('act',"") );

        if (!$id) {
            return $this->renderJson( [],'请选择要操作的账号',-1 );

        }
        if ( !in_array($act, ["remove","recover"]) ) {
            return $this->renderJson( [],'操作有误，请重试',-1 );
        }

        $info =Member::find()->where(['id' => $id])->one();
        if (!$info) {
            return $this->renderJson( [],'你制定的账户不存在~~',-1 );
        }

        switch ($act) {
            case "remove":
                $info->status = 0;
                break;
            case "recover":
                $info->status = 1;
                break;
        }

        $info->updated_time = date("Y-m-d H:i:s");
        $info->update(0);
        return $this->renderJson( [],'操作成功~~' );

    }

}
