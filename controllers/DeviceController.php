<?php

namespace app\controllers;

use app\models\AccessToken;
use app\models\Device;
use app\models\HistoryBarrier;
use app\models\JournalSendData;
use app\models\ListOfDebtor;

use app\models\LoginForm;
use app\models\Region;
use app\models\User;
use app\models\UserDevice;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class DeviceController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['authorization', 'index', 'debtor-list', 'get-debtor-list', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Авторизация пользвоателя.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $model = new User();

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->auth()) {
                return $this->redirect('authorization');
            } else {
                \Yii::$app->getSession()->setFlash('danger', 'Ошибка авторизации');
                return $this->render('login', ['model' => $model]);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Авторизация устройства
     * @return string|Response
     */
    public function actionAuthorization()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect("login");
        }

        $device = new Device();

        if(\Yii::$app->request->post()){
            $post = \Yii::$app->request->post('Device');
            $login = array_key_exists('login', $post) ? $post['login'] : null;
            $password = array_key_exists('password', $post) ? $post['password'] : null;

            $token = $device->getTokenAuth($login, $password);

            $conver_token = $token;
            $device->updateLastConnection($conver_token);

            if (is_bool($device->authConnectionGetDataDebtor($conver_token))){
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

    /**
     * Характеристики устройства
     * @param $pages
     * @return string|Response
     */
    public function actionIndex($pages=null)
    {
        if ($pages == null) {
            return $this->redirect("authorization");
        }

        if (Device::findPages($pages)){
            return $this->render('index', [
                'status' => false,
            ]);
        }

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


    /**
     * Списки посетителей
     * @param $pages
     * @return string|Response
     */
    public function actionDebtorList($pages=null)
    {
        if ($pages == null) {
            return $this->redirect("authorization");
        }


        $list = new ListOfDebtor();
        $dataProvider = $list->dataProviderDebtorList();

        return $this->render('debtor-list', [
            'pagesStatus' => false,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Экшен кнопки обновить данные
     * @param $pages
     * @return Response | string
     * @throws Exception|\Exception
     */
    public function actionGetDebtorList($pages)
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect("authorization");
        }

        if ($pages != null){
            $data = Device::getInfo($pages);
        } else {
            $data = false;
        }

        if (is_bool($data)){
            $journal = JournalSendData::getJournal();
            $device = is_bool(Device::deviceModelFindOnToken($pages)) ? new Device() : Device::deviceModelFindOnToken($pages);

            \Yii::$app->getSession()->setFlash('danger', 'Ошибка получения данных');
            return $this->render('index', [
                'device' => $device,
                'journal' => $journal,
                'status' => false,
            ]);
        } else {

            Device::saveReceived($data, $pages);
            $device = Device::updateLastConnection($pages);

            \Yii::$app->getSession()->setFlash('success', 'Данные получены');

            return $this->redirect(['index',
                'pages' => $pages,
                'device' => $device,
                'status' => true,
            ]);
        }
    }

    /** Экшен отправки истории посещения
     * @return bool
     */
    public function actionSendJournal()
    {
        $history = new HistoryBarrier();
        $data = $history->collectHistoryJournal();

        $token = AccessToken::findCurrentDevice($history->company_device);
        $sendStatus = Device::sendJournal($data, $token);

        if (!$sendStatus){
                //Запуск питоновского скрипта который опять запросит данный экшен
        } else {
            JournalSendData::sendHistory($data);
            HistoryBarrier::saveNewSendInInom();
        }

        return $sendStatus;
    }

    /**
     * Экшен выхода из профиля устройства
     * @return Response
     */
    public function actionLogout()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect("login");
        }

        \Yii::$app->user->logout();

        return $this->redirect('login');
    }

//    public function actionAddNewGuest($pages)
//    {
//         $guest = new ListOfDebtor();
//         $region = new Region();
//         $device = is_bool(Device::deviceModelFindOnToken($pages)) ? new Device() : Device::deviceModelFindOnToken($pages);;
//
//         if ($guest->load(\Yii::$app->request->post()) && $region->load(\Yii::$app->request->post())){
//             $postGuest = \Yii::$app->request->post('ListOfDebtor');
//             $postRegion = \Yii::$app->request->post('Region');
//
//             $region_id = array_key_exists('region_id', $postRegion) ? $postRegion['region_id'] : null;
//             $inom_id = Region::findInomId($region_id);
//             $phone = array_key_exists('phone', $postGuest) ? ListOfDebtor::formPhone($postGuest['phone']) : null;
//
//             $guest->phone =  $phone;
//             $guest->created_at = time();
//             $guest->type_sync = 4;
//             $guest->inom_id = $inom_id;
//
//             $region->region_id = $region_id;
//             $region->inom_id = $inom_id;
//
//             if ($guest->save() && $region->save() && !ListOfDebtor::validateNumber($phone)) {
//
//                 return $this->redirect(['debtor-list', 'pages'=>$pages]);
//             } else {
//                 \Yii::$app->getSession()->setFlash('danger', 'Ошибка сохранения');
//                 return $this->render('add-guest', [
//                     "guest" => $guest,
//                     "region" => $region
//                 ]);
//             }
//         } else {
//             return $this->render('add-guest', [
//                 "guest" => $guest,
//                 "region" => $region
//             ]);
//         }
//    }
}