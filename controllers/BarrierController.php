<?php 
    namespace app\controllers;

    use app\models\ListOfDebtors;
    use yii\web\Controller;

    class BarrierController extends Controller
    {
        public function actionNumber($number){
            return $number;
        }
        public function actionDebtor($number)
        {
            $query = ListOfDebtors::find();
            $user = $query
                ->where('number = :number', [':number'=>$number])
                ->one();
            if ($user){
                if ($user->debt >= 350) {
                    echo "0; 0 - всё OK" . "</br> 1; $user->sender - должен $user->debt денег";
                }else {
                    
                    $e = escapeshellcmd('/home/admin/Desktop/backend/web/rele.py');
                    $command = exec($e);
                    echo "<br/>";
                    echo exec("ls -l");
                    print_r($command);
                    //echo "0; 0 - всё OK" . $command;
                }
            }
        }
    }
