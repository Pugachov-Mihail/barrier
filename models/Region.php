<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Участки
 * @property int $id
 * @property int $inom_id id физ лица полученного из Ином
 * @property int $account_id лицевой счет
 * @property int $company_id компания
 * @property int $region_id участок
 * @property int $name_region наименование участка
 */

class Region extends ActiveRecord
{

}