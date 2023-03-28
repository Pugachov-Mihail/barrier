<?php

namespace app\controllers;

use app\models\HistoryBarrier;
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
        if($result = preg_match("/[0-9]{0,11}/", $number)) {
            if (strlen($number) == 11) {
                $model = new ListOfDebtor();

                if ($model->getDebtor($number)) {
                    HistoryBarrier::writeHistory($number, 1);
                }
            } else {
                echo "Некорректный номер";
                return;
            }
        }
    }
}