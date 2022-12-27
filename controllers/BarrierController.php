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
            $user = $query
                ->where('number = :number', [':number'=>$number])
                ->one();
            if ($user){
                if ($user->debt >= 350) {
                    echo "0; 0 - всё OK" . "</br> 1; $user->sender - должен $user->debt денег";
                }else {
                    $command = exec('rele.py');
                    echo $command;
                }
            }
        }
    }
