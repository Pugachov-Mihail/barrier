<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Журнал отправок данных в Апи Inom
 * @property int $id
 * @property json $json_object json объект с данными, которые были отправленны
 * @property int $date_send дата и время отправки
 * @property bool $state_response состояние ответа
 */
class JournalSendData extends ActiveRecord
{
    public function attributeLabels()
    {
        return [
            'json_object' => 'json объект с данными, которые были отправленны',
            'date_send' => 'дата и время отправки',
            'state_response' => 'состояние ответа',
        ];
    }

}