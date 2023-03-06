<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Устройство
 * @property int $id
 * @property int $id_device id устройства
 * @property int $company_id компания
 * @property int $ip_sluice шлюз Апи
 */
class Device extends ActiveRecord
{

}