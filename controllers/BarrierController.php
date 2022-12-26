<?php 
    namespace app\controllers;

    use app\models\BlackList;
    use yii\web\Controller;

    class BarrierController extends Controller
    {
        public function actionNumber($number){
            return $number;
        }
        public function actionDebtor($number)
        {
            $query = BlackList::find();
            $user = $query->filterWhere(['number'=>$number]);

            return "$user";
        }
    }
