<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Участки
 * @property int $id
 * @property int $list_debtor_id id физ лица полученного из Ином
 * @property int $account_id лицевой счет
 * @property int $company_id компания
 * @property int $region_id участок
 * @property int $inom_id
 * @property int $name_region наименование участка
 */

class Region extends ActiveRecord
{
    public static function saveRegion($accounts, $id, $inom_id)
    {

        if ($accounts != null){
            $model = new self();

            if ($accounts->number != null){
                $model->account_id = $accounts->number;
            }
            if ($accounts->sector != null){
                $model->region_id = $accounts->sector;
            }
            $model->list_debtor_id = $id;
            $model->inom_id = $inom_id;

            $insert = $model->insert();

            if (!$insert) {
                throw new Exception("Ошибка сохранения данных");
            } else {
                return true;
            }
        }
        return false;
    }

    private static function findRegionUser($id)
    {
        $model = self::find()
            ->where(['=', 'list_debtor_id', $id])
          //  ->andWhere(['=', 'inom_id', $inom_id])
            ->all();

        return !is_object($model) ? $model : null ;
    }


    public static function returnRegion($id, $inom_id)
    {
        $regionFind = self::findRegionUser($id);
        $regionOne = self::find()->where(['=', 'inom_id', $inom_id])->all();
        $result = '';

        foreach ($regionFind as $values) {
            foreach ($regionOne as $value){
                if ($values->inom_id == $value->inom_id){
                    if ($values->region_id != $value->region_id){
                        $result = $values->region_id . " , " . $value->region_id;
                    } else {
                        if ($values->region_id != $value->region_id) {
                            $result = $values->region_id;
                        }
                    }
                }
            }
        }

        return $result;
    }

//if (($value->inom_id == $regionOne->inom_id)) {
//$result = $result . " " . $value->region_id;
//}

}