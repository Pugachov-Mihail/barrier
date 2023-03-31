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

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'list_debtor_id' => 'Должник',
            'account_id' => 'Лицевой счет',
            'company_id' => 'Компания',
            'region_id' => 'Номер участка',
            'name_region' => 'Название участка',
        ];
    }

    public static function saveRegion($accounts, $id, $inom_id, $company_id)
    {
        if ($accounts != null){

            foreach ($accounts as $value){
                $model = new self();

                if ($value->number != null && $value->number >= 0){
                    $model->account_id = $value->number;
                }
                if ($value->sector != null && $value->sector >= 0){
                    $model->region_id = $value->sector;
                }
                if ($company_id != null && $company_id >= 0){
                    $model->company_id = $company_id;
                }
                if ($id != null && $id >= 0){
                    $model->list_debtor_id = $id;
                }
                if ($inom_id != null && $inom_id >= 0){
                    $model->inom_id = $inom_id;
                }
                $insert = $model->insert();
            }


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
        $model = self::find()->where(['=', 'inom_id', $id])->all();

        return !is_object($model) ? $model : null ;
    }

    public static function perrmissionOnSave($id, $region, $account)
    {
        $model = self::findRegionUser($id);

        if ($model != null){
            if($model->region_id != $region || $model->account != $account){
                //update
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function updateRegion($id, $account)
    {
        $model = self::findRegionUser($id);

        if ($model != null){
            foreach ($account as $value){
                $model->updateAttributes([
                    'account_id' => $value->number,
                    'region_id' => $value->sector
                ]);
            }

            $insert = $model->insert();

            if (!$insert) {
                throw new Exception("Ошибка сохранения данных");
            } else {
                return $model;
            }
        } else {
            return false;
        }
    }

    public static function returnRegion($inom_id)
    {

            $regionFind = self::findRegionUser($inom_id);
            $result = [];
            $text = '';

        if ($regionFind != null){
            foreach ($regionFind as $values) {
                if (in_array($values->inom_id, $result)) {
                    continue;
                } else {
                    if (self::counterArrayResult($values->region_id, $result, $inom_id)) {
                        $result[$values->inom_id][] = $values->region_id;
                    }
                }
            }

            foreach ($result[$inom_id] as $value){
                $text .= $value . ", ";
            }

            return mb_substr($text, 0, -2);
        } else {
            return "--";
        }
    }

    public static function counterArrayResult($value, $array, $key)
    {
        $count = 0;
        if ($array[$key] != null){
            sort($array[$key]);
            foreach ($array[$key] as $values){
                if ($values == $value){
                    $count++;
                } else {
                    continue;
                }
            }
        } else {
            return true;
        }

        if($count < 1){
            return true;
        } else {
            return false;
        }
    }

    public static function findInomId($region)
    {
        if ($region == null){
            return null;
        } else {
            $model = self::find()
                ->where(['=', 'region_id', $region])
                ->one();

        }
        return $model != null ? $model->inom_id : null;
    }
}