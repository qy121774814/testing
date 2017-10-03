<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 15:48
 */

namespace app\commands;


use yii\console\Controller;

class BaseController extends Controller
{
    public function echoLog( $msg )
    {
        echo date( "Y-m-d H:i:s" ).":".$msg . "\r\n";
        return true;
    }
}
