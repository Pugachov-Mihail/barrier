<?php

namespace app\controllers;

use app\models\ListOfDebtors;
use app\models\UploadForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;

class SiteController extends Controller
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
                        'actions' => ['logout'],
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
       // $this->csvReader();
        return $this->render('index');
    }

    // функция добавления в csv - LPList
    public function actionCsvReader(){
        //Название таблицы в малине
        $query = ListOfDebtors::find()->all();
        $fp = fopen('php://output', 'w');
        if ($fp)
        {
            // Перебор и запись в файл
            foreach ($query as $value) {
                fputs($fp,
                    "$value->id; $value->number; $value->sender;\r\n"); ;
            }
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="export_' . date('d.m.Y') . '.csv"');
        }
        fclose($fp);
        die;
    }
//Страница с загрузкой Csv файла
    public function actionLoadCsvFile(){
        $model = new UploadForm();
        Yii::$app->language = 'ru-RU';
        if (Yii::$app->request->isPost){
            if ($model->upload()){
                echo $model->csvFile . '<br/>';
                $parsing = $model->parsingCsvFile($model->csvFile);
                //$a = $model->saveDataCsvFileDb($parsing, $model);
                 //$this->redirect(['index']);
                return $parsing;
            }
        }
        return $this->render('loadCsv', ['model'=>$model]);
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionDebtor(){
        return "hello";
    }
}
