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
    /** Возвращает сумму долга
     * @param $id
     * @return mixed|null
     */
    public static function findCredit($id)
    {
        $model = self::findDebtor($id);
        return $model->credit;
    }

    /**
     * Поиск по связанной таблице list_debtor
     * @param $id
     * @return array|ActiveRecord|null
     */
    public static function findDebtor($id)
    {
        $model = self::find()
            ->where(['=', 'list_debtor_id', $id])
            ->one();

        return $model != null ? $model : null;
    }

    /**
     *  Обновление долга у текущего посетителя
     * @param $id
     * @param $debtor
     * @param $credit
     * @return array|false|ActiveRecord
     * @throws Exception
     * @throws \Throwable
     */
    public static function updateThisDebtor($id, $debtor, $credit)
    {
        $model = self::findDebtor($id);

        if($model != null){
            $model->updateAttributes([
                'credit' => $credit,
                'debt' => $debtor
            ]);

            return $model;
        } else {
            return false;
        }
    }
}