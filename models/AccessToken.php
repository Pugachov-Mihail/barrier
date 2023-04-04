<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int id
 * @property int id_device
 * @property string token
 * @property int created Дата создания токена в unix-time
 * @property int status
 * @property int company_id
 * @property int created_at
 */
class AccessToken extends ActiveRecord
{
    public static function findCurrentToken($token)
    {
        $result = self::find()->where(["=", "token", $token])->all();
        return count($result) > 1 ? false : $result;
    }

    public static function findToken($token)
    {
        return self::find()->where(["=", "token", $token])->one();
    }

    public static function findCurrentDevice($company_id)
    {
        $device = Device::findDeviceOnSend($company_id);

        $model = self::find()->where(['=', 'id', $device->id])->one();

        return $model != null ? $model->token : null;
    }
}