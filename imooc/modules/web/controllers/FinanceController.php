<?php

namespace app\modules\web\controllers;

use app\modules\web\controllers\common\BaseController;

class FinanceController extends BaseController
{
        public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module,$config);
        $this->layout = "main";
    }
    //订单列表
    public function actionIndex()
    {

        return $this->render( "index" );
    }
    //财务流水
        public function actionAccount()
    {

        return $this->render( "account" );
    }
    //订单详情
    public function actionPay_info()
    {

        return $this->render( "pay_info" );
    }
 

}
