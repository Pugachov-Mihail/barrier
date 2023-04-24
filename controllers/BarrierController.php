<?php

namespace app\controllers;

use app\models\HistoryBarrier;
use app\models\ListOfDebtor;
use app\models\MessageForDebtor;
use yii\web\Controller;

class BarrierController extends Controller
{
    /** Экшен открытия шлагбаума
     * @param $message
     * @return false|string
     */
    public function actionOpenBarrier($message){
        if($message=="OPEN_GATE"){
            //exec("sudo -u www-data sudo python assets/rele.py");
            return json_encode(['status' => "Все ОК"]);
        } elseif ($message=="DONT_OPEN"){
            return json_encode(['status' => "dont_open"]);
        }else{
            return json_encode(['status' => "error"]);
        }
    }

    /** Получает номер телефона от атс и передает ей json с дальнейшими действиями
     * @param $number
     * @return false|string|null
     */
    public function actionDebtor($number)
    {
        if($result = preg_match("/[0-9]/", $number)) {
            if (strlen($number) == 11) {
                $model = new ListOfDebtor();
                $openGate = ListOfDebtor::findNumber($number);

                if(is_object($openGate)) {
                    if ($model->getDebtorByPhone($number)) {
                        HistoryBarrier::writeFamouseHistory($number, $openGate->open_gate);
                        return MessageForDebtor::getMessage($number);

                    } else {
                        HistoryBarrier::writeFamouseHistory($number, $openGate->open_gate);
                        return MessageForDebtor::getFeedbackGuest($number);
                    }
                } else {
                    HistoryBarrier::writeUnknownPhone($number, 0);
                    $message = ['unknown' => 'unknown phone'];
                    return json_encode($message);
                }
            } else {
                $message = ['error' => 'error'];
                return json_encode($message);
            }
        }
        $message = ['error' => 'error'];
        return json_encode($message);
    }

}