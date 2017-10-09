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
use \app\models\members\Member;
use app\common\services\UploadService;

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
                case "bind":
                    $this->handleBind();
                    break;
                case "member_avatar":
                    $this->handleMemberAvatar( $_item );
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

    /*
     * 绑定微信相关通知
     * */
    private function handleBind($item)
    {
        $data = @json_encode( $item['data'] , true);

        if( !isset( $data['member_id'] )|| !isset( $data['openid'] ) ){
            return false;
        }

        if( !$data['member_id']  || !$data['openid']  ){
            return false;
        }

        $member_info = Member::findOne( [ 'id' => $data['member_id'] ] );
        if ( !$member_info ){
            return false;
        }

        TemplateService::bindNotice( $data['member_id'] );
        return true;

    }

    /*
     * 更新头像
     * */

    private function handleMemberAvatar( $item ){
        $data = @json_decode( $item['data'],true );

        if( !isset( $data['member_id'] ) || !isset( $data['avatar_url']) ){
            return false;
        }


        if( !$data['member_id'] || !$data['avatar_url'] ){
            return false;
        }

        $member_info = Member::findOne([ 'id' => $data['member_id'] ]);
        if( !$member_info ){
            return false;
        }

        $ret = UploadService::uploadByUrl( $data['avatar_url'],"avatar" );
        if( $ret ){
            $member_info->avatar = $ret['path'];
            $member_info->update( 0 );
        }
        return true;
    }
}