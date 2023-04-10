<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Сообщения для должника
 * @property int $id
 * @property int $list_debtor_id id физ. лица
 * @property int $phone номер телефона должника
 * @property int $type_scenary тип сценария
 * @property int $feedback обратная связь
 * @property int $create_at время создания
 * @property int $inom_id
 * @property int $company_id компания
 * @property int $company_name название компании
 */
class MessageForDebtor extends ActiveRecord
{
    public static function findMessage($id)
    {
        return self::find()
        ->where(['=', 'id', $id])
        ->one();
    }

    private static function findMessageByPhone($number)
    {
        $model = self::find()->where(['=', 'phone', $number])->one();

        return $model != null ? $model : null;
    }

    private static function getMessageTypeOwner($typeAction, $debtor, $list, $regions)
    {
        $message = [
            'type_action' => $typeAction->type_action,
            'credit' => $debtor->credit
        ];

        if ($typeAction->type_action == 0){
            $message['date_sound'] = $list->date_sound;
        }

        $region = [];
        foreach ($regions as $value){
                print_r($value);
        }

        return $message;
    }

    public static function getMessage($number)
    {
        $model = self::findMessageByPhone($number);

        if ($model != null){
            $debtor = Debtor::findDebtor($model->list_debtor_id);
            $list = ListOfDebtor::findNumber($number);

            if($list->self_id != null){
                $region = Region::findListDebtor($model->list_debtor_id);

                if(is_array($region)){
                    foreach ($region as $value){
                        if ($value->region_id){
                            $region = $value;
                        }
                    }
                }
            }

            return $region;
//            $message = [
//                'template_id' => $model->type_pattern,
//                'args' =>
//            ];
        }
    }
}
