<?php

namespace app\modules\web\controllers;

use app\modules\web\controllers\common\BaseController;

class DashboardController extends BaseController
{
	    public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module,$config);
        $this->layout = "main";
    }
    //登录页面
    public function actionIndex()
    {

        return $this->render( "index" );
    }


}
