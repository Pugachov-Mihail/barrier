<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Model History Barrier
 * @property int $id
 * @property string $device_id
 * @property string $phone номер телефона звонившего на шлагбаум
 * @property int $datetime Дата и время открытия шлагбаума
 * @property int $type тип открытия (въезд/выезд)
 * @property int $message_id id сообщения зачитанного звонящему
 * @property int $open_gate действие (открылся / не открылся)
 * @property int $send_in_inom отправлено на сервер Ином
 * @property int $company_id компания
 * @property int $type_user
 * @property int $inom_id
 * @property int $company_name название
 */
class HistoryBarrier extends ActiveRecord
{
    public $company_device;

    public static $sendInfo = [
        0 => "Не отправлено",
        1 => "Отправлено"
    ];

    public static function writeFamouseHistory($phone, $open_gate)
    {
        $model = new self();
        $list_of_debtor = ListOfDebtor::findNumber($phone);
        $message =  MessageForDebtor::findMessage($list_of_debtor->id);

        if($list_of_debtor != null && $message != null){
            $model->phone = $list_of_debtor->phone;
            $model->message_id = $message->feedback;
            $model->type_user = $list_of_debtor->type_user;
            $model->inom_id = $list_of_debtor->inom_id;
            $model->datetime = time();
            $model->open_gate = $open_gate;
            $model->company_id = $message->company_id;
            $model->send_in_inom = 0;
        } else {
            return false;
        }

        if ($model->save()) {
            return true;
        }else {
            return false;
        }
    }

    public static function writeUnknownPhone($phone, $open_gate)
    {
        $model = new self();
        $device = Device::find()->all();

        $id = '';

        foreach ($device as $values){
            foreach ($values as $key => $value){
                if (end($device)) {
                    if ( $key == 'company_id') {
                        $id = $value;
                    }
                }
            }
        }

        if (!ListOfDebtor::validateNumber($phone)) {
            $model->phone = $phone;
            $model->type_user = 3;
            $model->datetime = time();
            $model->open_gate = $open_gate;
            $model->company_id = $id;
            $model->send_in_inom = 0;
        }

        if ($model->save()) {
            return true;
        }else {
            return false;
        }
    }

    public function collectHistoryJournal()
    {
        $model = self::find()->all();
        $data = [];
        $arr = [];

        foreach ($model as $values){
            foreach ($values as $key => $value) {
                if ($key == 'send_in_inom' || $key == 'company_name'){
                    continue;
                } else {
                    $arr[$key] = $value;
                }

                if ($key == array_key_first($model)){
                    if($key == 'company_id'){
                        $this->company_device = $value;
                    }
                }

                if ($key == 'company_id') {
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