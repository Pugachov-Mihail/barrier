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
            if(preg_match("/[0-9]{0,11}/", $number)){
                if(strlen($number)==11) {
                    $query = ListOfDebtors::find();

                    $user = $query
                        ->where('number = :number', [':number' => $number])
                        ->one();
                    if ($user) {
                        if ($user->debt >= 350) {
                            echo "0; 0 - всё OK" . "</br> 1; $user->sender - должен $user->debt денег";
                        } else {
                            $e = escapeshellcmd('/home/admin/Desktop/backend/web/rele.py');
                            $command = exec($e);
                            echo "<br/>";
                            echo exec("a.php");
                            print_r($command);
                            //echo "0; 0 - всё OK" . $command;
                        }
                    }
                }else{
                    echo "Короткий номер";
                }
            } else{
                echo "Некорректный номер";
            }
        }
    }
