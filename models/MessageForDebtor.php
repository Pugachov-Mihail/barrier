<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Сообщения для должника
 * @property int $id
 * @property int $id_inom id физ. лица
 * @property int $phone номер телефона должника
 * @property int $type_scenary тип сценария
 * @property int $feedback обратная связь
 * @property int $create_at время создания
 */
class MessageForDebtor extends ActiveRecord
{

}