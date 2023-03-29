<?php

namespace app\controllers;

use app\models\Device;
use app\models\HistoryBarrier;
use app\models\JournalSendData;
use app\models\ListOfDebtor;

use yii\base\Exception;
use yii\web\Controller;
use yii\web\Response;

class DeviceController extends Controller
{
    public function actionAuthorization()
    {
        $device = new Device();
        if(\Yii::$app->request->post()){
            $post = \Yii::$app->request->post('Device');
            $login = array_key_exists('login', $post) ? $post['login'] : null;
            $password = array_key_exists('password', $post) ? $post['password'] : null;

            $token = $device->getTokenAuth($login, $password);

            $conver_token = $token;
            $device->updateLastConnection($conver_token);

            if (is_bool(Device::authConnectionGetDataDebtor($conver_token))){
                \Yii::$app->getSession()->setFlash('danger', 'Ошибка авторизации устройства');

                return $this->render('authorization', ['device'=>$device]);
            } else {
                \Yii::$app->getSession()->setFlash('success', 'Авторизация успешна');

                return $this->redirect(['index', 'pages'=>$conver_token, 'status' => true]);
            }
        } else {
            return $this->render('authorization', ['device'=>$device]);
        }
    }

    public function actionIndex($pages=null)
    {
        $journal = JournalSendData::getJournal();

        if ($pages != null){
            $device = is_bool(Device::deviceModelFindOnToken($pages)) ? new Device() : Device::deviceModelFindOnToken($pages);

            return $this->render("index", [
                'device' => $device,
                'journal' => $journal,
                'status' => true
            ]);

        } else {
            \Yii::$app->getSession()->setFlash('danger', 'Ошибка получения данных');
            return $this->render('index', ['status' => false]);
        }
    }


    public function actionDebtorList($pages)
    {
        $list = new ListOfDebtor();
        $dataProvider = $list->dataProviderDebtorList();

        return $this->render('debtor-list', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Экшен кнопки обновить данные
     * @param $pages
     * @return Response
     * @throws Exception|\Exception
     */
    public function actionGetDebtorList($pages)
    {
        if ($pages != null){
            $data = Device::getInfo($pages);
        } else {
            $data = false;
        }

        if (is_bool($data)){
            \Yii::$app->getSession()->setFlash('danger', 'Ошибка получения данных');
            // Написать скрипт который будет запрашивать повторно если ошибка
//            $device = Device::deviceModelFindOnToken($pages);
            return $this->redirect('index');
        } else {
            Device::saveReceived($data, $pages);
            $device = Device::updateLastConnection($pages);

            \Yii::$app->getSession()->setFlash('success', 'Данные получены');

            return $this->redirect(['index',
                'pages' => $pages,
                'device' => $device,
                'status' => true
            ]);
        }
    }

    public function actionSendJournal()
    {
        $data = HistoryBarrier::sendHistoryJournal();
        $sendStatus = Device::sendJournal($data);

        if (is_bool($sendStatus)){
                //Запуск питоновского скрипта который опять запросит данный экшен
        } else {
            JournalSendData::sendHistory($data);
            HistoryBarrier::saveNewSendInInom();
        }

        return $sendStatus;
    }
}