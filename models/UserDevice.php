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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Логин',
            'password' => 'Пароль',
        ];
    }

    public static function findByUsername($name)
    {
        $model = self::find()->where(['=', 'name', $name])->one();

        return $model != null ? $model : null;
    }



    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public function login()
    {
        $model = self::find()
            ->where(['=', 'name', $this->name])
            ->where(['=', 'password', $this->password])
            ->one();

        return $model != null ? true : false;
    }
}