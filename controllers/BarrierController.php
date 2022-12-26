<?php 
    namespace app\controllers;

    use yii\web\Controller;

    class BarrierController extends Controller
    {
        public function actionNumber($number){
            return $number;
        }
        public function actionDebtor($must, $count)
        {
            #$request = Yii::$app->request;
            #$body = $request -> bodyParams;
            $massage = "0;0 - все ok \n
                1;". $must . " должен " . $count . " денег ";
            return $massage;
        }
    }
