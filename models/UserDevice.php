<?php

namespace app\models;

use yii\db\ActiveRecord;


/**
 * Пользователи устройства
 * @property int $id
 * @property int $id_inom id пользователя
 * @property int $name имя пользователя
 * @property int $password пароль
 * @property int $password_reset хеш пароль при сбросе пароля
 * @property int $create_at дата регистрации
 * @property int $update_at дата обновления
 */
class UserDevice extends ActiveRecord
{

}