<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

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
    public static function saveRegion($accounts)
    {
        if ($accounts != null){
            $model = new self();

            if ($accounts->number != null){
                $model->account_id = $accounts->number;
            } else {
                return 1;
            }
            if ($accounts->sector != null){
                $model->region_id = $accounts->sector;
            } else {
                return 2;
            }

            $insert = $model->insert();
            if (!$insert) {
                throw new Exception("Ошибка сохранения данных");
            } else {
                return true;
            }
        }
        return false;
    }

}