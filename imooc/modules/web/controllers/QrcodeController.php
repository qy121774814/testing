<?php

namespace app\modules\web\controllers;

use app\modules\web\controllers\common\BaseController;


class QrcodeController extends BaseController
{
        public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module,$config);
        $this->layout = "main";
    }
    //渠道二维码列表
    public function actionIndex()
    {

        return $this->render( "index" );
    }
    //渠道二维码的编辑或者添加
        public function actionSet()
    {

        return $this->render( "set" );
    }


}
