<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/7
 * Time: 23:30
 */
use \app\models\QueueList;
use \app\commands\BaseController;
use\app\common\services\weixin\TemplateService;

class ListController extends BaseController
{
    /*
     * php yii queue/list/run
     * */
    public function actionRun()
    {
        $list = QueueList::find()->where( [ 'status => -1' ] )->orderBy([ 'id' => SORT_ASC])->limit( 10 )->all();
        if( !$list ){
            return $this->echoLog( "no data to handle~~" );
        }
        foreach( $list as $_item ){
            $this->echoLog( "queue_id:{$_item['id']}" );
            switch($_item['queue_name']){
                case "pay":
                    $this->handlepay( $_item );
                    break;
            }
            $_item->status = -1;
            $_item->updated_time = date( "Y-m-d H:i:s" );
            $_item->save(0);

        }

    }

    private function handlepay( $item ){
        $data = @json_decode($item['data'],true);
        if( !isset( $data['member_id'] )|| !isset( $data['pay_order_id'] ) ){
            return false;

        }
        TemplateService::payNotice( $data['pay_order_id'] );

    }

}