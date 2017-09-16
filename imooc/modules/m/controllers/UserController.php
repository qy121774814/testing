<?php

namespace app\modules\m\controllers;

use yii\web\Controller;
use app\common\services\ConstantMapService;
use app\common\services\DataHelper;
use app\common\services\QueueListService;
use app\common\services\UrlService;
use app\common\services\UtilService;
use app\models\book\Book;
use app\models\City;
use app\models\members\Member;
use app\models\members\MemberAddress;
use app\models\members\MemberComments;
use app\models\members\MemberFav;
use app\models\members\OauthMemberBind;
use app\models\pay\PayOrder;
use app\models\pay\PayOrderItem;
use app\models\sms\SmsCaptcha;
use app\modules\m\controllers\common\BaseController;
use app\common\services\AreaService;



class UserController extends BaseController
{
        public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module,$config);
        $this->layout = "main";
    }
    //账号绑定
    public function actionBind()
    {

        if (\Yii::$app->request->isGet) {
            return $this->render("bind");
        }

        $mobile = trim($this->post("mobile"));
        $img_captcha = trim($this->post("img_captcha"));
        $captcha_code = trim($this->post("captcha_code"));
        $date_now = date("Y-m-d H:i:s");

        $openid = $this->getCookie($this->auth_cookie_current_openid);

        if (mb_strlen($mobile, "utf-8") < 1 || !preg_match("/^[1-9]\d{10}$/", $mobile)) {
            return $this->renderJSON([], "请输入符合要求的手机号码~~", -1);
        }

        if (mb_strlen($img_captcha, "utf-8") < 1) {
            return $this->renderJSON([], "请输入符合要求的图像校验码~~", -1);
        }

        if (mb_strlen($captcha_code, "utf-8") < 1) {
            return $this->renderJSON([], "请输入符合要求的手机验证码~~", -1);
        }


        if (!SmsCaptcha::checkCaptcha($mobile, $captcha_code)) {
            return $this->renderJSON([], "请输入正确的手机验证码~~22", -1);
        }

        $member_info = Member::find()->where(['mobile' => $mobile, 'status' => 1])->one();

        if (!$member_info) {
            if (Member::findOne(['mobile' => $mobile])) {
                $this->renderJSON([], "手机号码已注册，请直接使用手机号码登录~~", -1);
            }

            $model_member = new Member();
            $model_member->nickname = $mobile;
            $model_member->mobile = $mobile;
            $model_member->setSalt();
            $model_member->avatar = ConstantMapService::$default_avatar;
            $model_member->reg_ip = sprintf("%u", ip2long(UtilService::getIP()));
            $model_member->status = 1;
            $model_member->created_time = $model_member->updated_time = date("Y-m-d H:i:s");
            $model_member->save(0);
            $member_info = $model_member;
        }

        if (!$member_info || !$member_info['status']) {
            return $this->renderJSON([], "您的账号已被禁止，请联系客服解决~~", -1);
        }

        if ($openid) {
            $bind_info = OauthMemberBind::find()->where(['member_id' => $member_info['id'], 'openid' => $openid, 'type' => ConstantMapService::$client_type_wechat])->one();

            if (!$bind_info) {
                $model_bind = new OauthMemberBind();
                $model_bind->member_id = $member_info['id'];
                $model_bind->type = ConstantMapService::$client_type_wechat;
                $model_bind->client_type = "weixin";
                $model_bind->openid = $openid;
                $model_bind->unionid = '';
                $model_bind->extra = '';
                $model_bind->updated_time = $date_now;
                $model_bind->created_time = $date_now;
                $model_bind->save(0);
//                //绑定之后要做的事情
//                QueueListService::addQueue( "bind",[
//                    'member_id' => $member_info['id'],
//                    'type' => 1,
//                    'openid' => $model_bind->openid
//                ] );
//            }
            }
        }

//
        if( UtilService::isWechat() && $member_info['nickname']  == $member_info['mobile'] ){
            return $this->renderJSON([ 'url' => UrlService::buildMUrl( "/oauth/login",[ 'scope' => 'snsapi_userinfo' ] )  ],"绑定成功~~11");
        }
        //todo设置登录态
        $this->setLoginStatus( $member_info );
        return $this->renderJSON(['url' => UrlService::buildMUrl("/default/index")], "绑定成功~~2");
    }
    //我的购物车
    public function actionCart()
    {

        return $this->render('cart');
    }
    //我的订单
    public function actionOrder()
    {

        return $this->render('order');
    }
    //我的
    public function actionIndex()
    {

        return $this->render('index');
    }
    //我的地址列表
    public function actionAddress()
    {
     
        return $this->render('address');
    }
    //编辑收货地址
    public function actionAddress_set()
    {
 
        return $this->render('address_set');
    }
    //我的收藏
    public function actionFav()
    {
    
        return $this->render('fav');
    }
    //我的评论列表
    public function actionComment()
    {
     
        return $this->render('comment');
    }
    //评论
    public function actionComment_set()
    {
     
        return $this->render('comment_set');
    }
}
