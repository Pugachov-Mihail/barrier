<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Models Debtor
 * @property int $id
 * @property int $inom_id id физ лица полученного из Ином
 * @property int $debt сумма долга
 * @property int $date_start_debt дата начала задолжности
 */

class Debtor extends ActiveRecord
{

}