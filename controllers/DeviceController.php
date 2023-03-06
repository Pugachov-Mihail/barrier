<?php

namespace app\controllers;

use app\models\Device;
use yii\web\Controller;

class DeviceController extends Controller
{
    public function actionAuthorization()
    {
        $device = new Device();
        if(\Yii::$app->request->post()){
            return;
        } else {
            return $this->render('authorization', ['device'=>$device]);
        }
    }
}