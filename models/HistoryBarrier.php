<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Model History Barrier
 * @property int $id
 * @property int $phone номер телефона звонившего на шлагбаум
 * @property int $date_open_barrier Дата и время открытия шлагбаума
 * @property int $open_type тип открытия (въезд/выезд)
 * @property int $id_message id сообщения зачитанного звонящему
 * @property int $open_gate действие (открылся / не открылся)
 * @property int $send_in_inom отправлено на сервер Ином
 * @property int $company_id компания
 * @property int $company_name название
 */
class HistoryBarrier extends ActiveRecord
{
    public static $sendInfo = [
        0 => "Не отправлено",
        1 => "Отправлено"
    ];

    public static function writeHistory($phone, $open_gate)
    {
        $model = new self();
        $list_of_debtor = ListOfDebtor::findNumber($phone);
        $message =  MessageForDebtor::findMessage($list_of_debtor->id);

        if($list_of_debtor != null && $message != null){
            $model->phone = $list_of_debtor->phone;
            $model->id_message = $message->feedback;
            $model->date_open_barrier = time();
            $model->open_gate = $open_gate;
            $model->company_id = $message->company_id;
            $model->company_name = $message->company_name;
            $model->send_in_inom = 0;
        }

        if ($model->save()) {
            return true;
        }else {
            return false;
        }
    }

    public static function sendHistoryJournal()
    {
        $model = self::find()->all();
        $data = [];
        $arr = [];

        foreach ($model as $values){
            foreach ($values as $key => $value) {
                $arr[$key] = $value;

                if ($key == 'company_name') {
                    $data[] = $arr;
                }
            }
        }

        return $data;
    }

    /**
     * @return array|ActiveRecord[]|null
     */
    private static function findDontSendHistory()
    {
        $model = self::find()->where(["<>", "send_in_inom", 1])->all();

        return is_array($model) ? $model : null;
    }

    public static function saveNewSendInInom()
    {
        $models = self::findDontSendHistory();

        if ($models != null){
            foreach ($models as $model){
                $model->send_in_inom = 1;
                $model->update(false);
            }
            return $models;
        } else {
            return false;
        }
    }
}