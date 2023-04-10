<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Журнал отправок данных в Апи Inom
 * @property int $id
 * @property json $data_response json объект с данными, которые были отправленны
 * @property int $date_send дата и время отправки
 * @property bool $response_status состояние ответа
 */
class JournalSendData extends ActiveRecord
{
    public static $statusType = [
        0 => "Не отправлены",
        1 => "Отправлены"
    ];

    public static function sendHistory($data)
    {
        if($data != null){
            $model = new self();

            $model->data_response = json_encode($data, JSON_UNESCAPED_UNICODE);
            $model->date_send = time();
            $model->response_status = true;

            if ($model->save()){
                return $model;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getJournal()
    {
        $models = self::find()
            ->where(['=', 'response_status', true])
            ->all();


        if (count($models) <= 1) {
           return $models == null ? new self() : self::find()->where(['=', 'id', 1])->one();
        } else {
            return JournalSendData::find()->orderBy(['id' => SORT_DESC])->offset(1)->one();
        }
    }

}