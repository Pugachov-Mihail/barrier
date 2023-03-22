<?php

namespace app\controllers;

use app\models\AccessToken;
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

            $conver_token = is_string($token) ? $token : $token->token;

            if (is_bool(Device::authConnectionGetDataDebtor($conver_token))){
                \Yii::$app->getSession()->setFlash('danger', 'Ошибка авторизации устройства');

                return $this->render('authorization', ['device'=>$device]);
            } else {
                \Yii::$app->getSession()->setFlash('success', 'Авторизация успешна');

                return $this->redirect(['index', 'pages'=>$conver_token]);
            }
        } else {
            return $this->render('authorization', ['device'=>$device]);
        }
    }

    public function actionIndex($pages)
    {
        $journal = new JournalSendData();

        $access_token = AccessToken::find()
            ->where(['=', 'token', $pages])
            ->one();
        $device = Device::find()
            ->where(['=', 'id', $access_token->id_device])
            ->one();

        return $this->render("index", [
            'device'=>$device,
            'journal' => $journal,
        ]);
    }

    public function actionGetDebtorList($pages)
    {
        $data = Device::getInfo($pages);
        if (is_bool($data)){
            \Yii::$app->getSession()->setFlash('danger', 'Ошибка олучения данных');
            return $this->render('index');

        } else {
            Device::saveReceived($data, $pages);
            \Yii::$app->getSession()->setFlash('success', 'Данные получены');
            return $this->redirect(['index', 'pages'=>$pages]);
        }
    }
}