<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @var int $id
 * @var double $log_time
 * @var string $category
 * @var string $prefix
 * @var string $message
 */
class Log extends ActiveRecord
{
    public static function getError()
    {
        $preResult = [];
        $data = [];
        $model = self::find()
            ->where(['=', 'level', 1])
            ->all();

        if(is_array($model)){
            foreach ($model as $value) {
                $preResult['level'] = $value->level;
                $preResult['category'] = $value->category;
                $preResult['log_time'] = $value->log_time;
                $preResult['message'] = $value->message;

                $data[] = $preResult;
            }
        }

        return $data;
    }
}