<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Сообщения для должника
 * @property int $id
 * @property int $list_debtor_id id физ. лица
 * @property int $phone номер телефона должника
 * @property int $type_scenary тип сценария
 * @property int $feedback обратная связь
 * @property int $create_at время создания
 * @property int $inom_id
 * @property int $company_id компания
 * @property int $company_name название компании
 */
class MessageForDebtor extends ActiveRecord
{
    public static function findMessage($id)
    {
        return self::find()
        ->where(['=', 'id', $id])
        ->one();
    }
}