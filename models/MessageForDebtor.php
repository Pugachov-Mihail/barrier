<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Сообщения для должника
 * @property int $id
 * @property int $list_debtor_id id физ. лица
 * @property int $phone номер телефона должника
 * @property int $type_scenary тип сценария
 * @property int $feedback обратная связь
 * @property int $create_at время создания
 * @property int $inom_id
 */
class MessageForDebtor extends ActiveRecord
{

}