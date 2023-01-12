<?php 
    namespace app\controllers;

    use app\models\ListOfDebtors;
    use yii\web\Controller;

    class BarrierController extends Controller
    {
        public function actionOpenBarrier($message){
            if($message=="Ok"){
                echo "0; 0 - всё OK";
                exec("sudo -u www-data sudo python assets/rele.py");
                return;
            }else{
                return;
            } 
        }

        public function actionDebtor($number)
        {
            if($result = preg_match("/[0-9]{0,11}/", $number)){
                if(strlen($number)==11) {
                    $query = ListOfDebtors::find();

                    $user = $query
 
                    ->where('number = :number', [':number' => $number])
                        ->one();
                    if ($user) {
                        if ($user->debt > 0) {
                            echo "1; $user->sender" . " " .  "$user->debt";
                            return;
                        } 
                        elseif ($user->vip = 1){
                            echo "0;-";
                            exec("sudo -u www-data sudo python assets/rele.py");
                            return;
                        }                  
                        else {
                            echo "0;-";
                            exec("sudo -u www-data sudo python assets/rele.py");
                            return;
                        }
                    }
                }else{
                    echo "Некорректный номер";
                    return;
                }
            } else{
                echo "Некорректный номер";
                return;
            }
            echo $result . "Error";
            return;
        }
    }
