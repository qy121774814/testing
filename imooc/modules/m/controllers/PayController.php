<?php

namespace app\modules\m\controllers;

use app\modules\m\controllers\common\BaseController;
use yii\web\Controller;
use app\common\services\UrlService;
use app\models\pay\PayOrder;


class PayController extends BaseController
{
    //购买支付
    public function actionBuy(){
        $pay_order_id = intval( $this->get("pay_order_id",0) );
        $reback_url = UrlService::buildMUrl("/user/index");
        if( !$pay_order_id ){
            return $this->redirect( $reback_url );
        }

        $pay_order_info = PayOrder::find()->where([ 'member_id' => $this->current_user['id'],'id' => $pay_order_id,'status' => -8 ])->one();
        if( !$pay_order_info ){
            return $this->redirect( $reback_url );
        }

        return $this->render('buy',[
            'pay_order_info' => $pay_order_info
        ]);
    }

    public function actionPrepare(){
        $pay_order_id = intval( $this->post("pay_order_id",0) );
        if( !$pay_order_id ){
            return $this->renderJSON( [],"系统繁忙，请稍后再试~~",-1 );
        }

        if( !UtilService::isWechat() ) {
            return $this->renderJSON([],"仅支持微信支付，请将页面链接粘贴至微信打开",-1);
        }

        $pay_order_info = PayOrder::find()->where([ 'member_id' => $this->current_user['id'],'id' => $pay_order_id,'status' => -8 ])->one();
        if( !$pay_order_info ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }

        $openid = $this->getOpenId();
        if( !$openid  ){
            $err_msg = "购买卡前请绑微信~~";
            return $this->renderJSON([],$err_msg,-1);
        }

        /*请求微信paysign或者支付宝接口*/

        $config_weixin = \Yii::$app->params['weixin'];
        $wx_target = new PayApiService( $config_weixin );

        $notify_url = $config_weixin['pay']['notify_url']['m'];//从配置中回去回调地址

        $wx_target->setParameter("appid",$config_weixin['appid']);
        $wx_target->setParameter("mch_id",$config_weixin['pay']['mch_id']);
        $wx_target->setParameter("openid",$openid);
        $wx_target->setParameter("body",$pay_order_info['note']);//商品描述
        $wx_target->setParameter("out_trade_no",$pay_order_info['order_sn'] );//商户订单号
        $wx_target->setParameter("total_fee",$pay_order_info['pay_price'] * 100 );//总金额
        $wx_target->setParameter("notify_url",UrlService::buildMUrl( $notify_url ) );//通知地址
        $wx_target->setParameter("trade_type","JSAPI");//交易类型

        $prepayInfo = $wx_target->getPrepayInfo();
        if(!$prepayInfo){
            return false;
        }

        $wx_target->setPrepayId($prepayInfo['prepay_id']);
        return $this->renderJSON( $wx_target->getParameters() );
    }
}
