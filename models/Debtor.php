<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Models Debtor
 * @property int $id
 * @property int $inom_id id физ лица полученного из Ином
 * @property int $debt сумма долга
 * @property int $date_start_debt дата начала задолжности
 * @property int $credit сумма задолжности
 */

class Debtor extends ActiveRecord
{
}