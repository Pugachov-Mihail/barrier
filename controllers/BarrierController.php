<?php

namespace app\controllers;

use app\models\ListOfDebtor;
use app\models\ListOfDebtors;
use yii\web\Controller;

class BarrierController extends Controller
{
    /**
     * Экшен открытия шлагбаума
     * @param $message
     * @return void
     */
    public function actionOpenBarrier($message){
        $list_of_debtor = new ListOfDebtor();
        if($message=="Ok"){
            echo "0; 0 - всё OK";
            //exec("sudo -u www-data sudo python assets/rele.py");
            $list_of_debtor->saveOpenGate($list_of_debtor);
            return;
        }else{
            echo "it";
            $list_of_debtor->saveDontOpenGate($list_of_debtor);
            return;
        }
    }

    public function actionDebtor($number)
    {
        if($result = preg_match("/[0-9]{0,11}/", $number)){
            if(strlen($number)==11) {
                $query = ListOfDebtor::find();

                $user = $query
                    ->where('number = :number', [':number' => $number])
                    ->one();
                if ($user) {
                    if ($user->debt > 0) {
                        echo "0; 0 - всё OK" . "</br> 1; $user->sender - должен $user->debt денег";
                        return;
                    } else {
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