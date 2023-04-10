<?php

namespace app\controllers;

use app\models\HistoryBarrier;
use app\models\ListOfDebtor;
use app\models\ListOfDebtors;
use app\models\MessageForDebtor;
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
            $list_of_debtor->saveOpenGate($list_of_debtor, 1);
            return;
        }else{
            echo "it";
            $list_of_debtor->saveOpenGate($list_of_debtor, 0);
            return;
        }
    }

    public function actionDebtor($number)
    {
        if($result = preg_match("/[0-9]/", $number)) {
            if (strlen($number) == 11) {
                $model = new ListOfDebtor();
                if ($model->getDebtorByPhone($number)) {
                    HistoryBarrier::writeFamouseHistory($number, 1);
                    return 'ok';
                } else {
                    //MessageForDebtor::
                    HistoryBarrier::writeUnknownPhone($number, 0);
                    return "Ну ты и черт";
                }
            } else {
                echo "Некорректный номер";
            }
        }
        return "Ну ты и черт 2";
    }

    public function actionA($number)
    {
        $a = MessageForDebtor::getMessage($number);
        print_r($a);
        return;
    }
}