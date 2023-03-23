<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Models Debtor
 * @property int $id
 * @property int $list_debtor_id id физ лица полученного из Ином
 * @property int $debt сумма долга
 * @property int $date_start_debt дата начала задолжности
 * @property int $credit сумма задолжности
 * @property int $id_debtor сумма задолжности
 * @property int $inom_id сумма задолжности
 */

class Debtor extends ActiveRecord
{
    public static function findDebtor($id)
    {
        $model = self::find()
            ->where(['=', 'list_debtor_id', $id])
            ->one();

        return $model->credit;
    }
}