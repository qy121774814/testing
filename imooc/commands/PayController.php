<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 15:34
 */

namespace app\commands;


use app\common\services\PayOrderService;
use app\models\pay\PayOrder;
use app\commands\BaseController;

class PayController extends BaseController
{
    /*
   *库存处理
   *释放30分钟前的订单
   * php yii pay/product_stock
   *
   * */
    public function actionProduct_stock()
    {
        $before_half_date = date( "Y-m-d H:i:s",time() - 30 * 60 );
        $before_half_order_list = PayOrder::find()->where( ['target_type' =>1 ,'status' => -8] )->andWhere([ '<=' ,'created_time',$before_half_date ])->all();
        if( !$before_half_order_list ){
            return $this->echoLog( "no data" );

        }

        foreach( $before_half_order_list as $_order_info ){
            PayOrderService::closeOrder( $_order_info );
        }
        return $this->echoLog( "it`s over ~~" );


    }

}