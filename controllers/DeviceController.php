<?php

namespace app\controllers;

use app\models\Device;
use app\models\JournalSendData;
use yii\web\Controller;

class DeviceController extends Controller
{
    public function actionAuthorization()
    {
        $device = new Device();
        if($device->load(\Yii::$app->request->post())){
            $post = \Yii::$app->request->post('Device');
            $login = array_key_exists('login', $post) ? $post['login'] : null;
            $password = array_key_exists('password', $post) ? $post['password'] : null;

            $token = Device::sendLoginAndPassword($login, $password);

            return Device::getInfo($token); // $this->render('authorization', ['device'=>$device,'token'=>$token]);
        } else {
            return $this->render('authorization', ['device'=>$device]);
        }
    }

    public function actionIndex()
    {
        $device = new Device();
        $journal = new JournalSendData();

        return $this->render("index", [
            'device'=>$device,
            'journal' => $journal,
        ]);
    }
}