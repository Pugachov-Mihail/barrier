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
    /** Поиск сценария для должников
     * @param $id
     * @return array|ActiveRecord|null
     */
    public static function findMessage($id)
    {
        return self::find()
        ->where(['=', 'id', $id])
        ->one();
    }

    /** Поиск сценария по телефону
     * @param $number
     * @return array|ActiveRecord|null
     */
    private static function findMessageByPhone($number)
    {
        $model = self::find()->where(['=', 'phone', $number])->one();

        return $model != null ? $model : null;
    }

    /** Возвращает тип обратной связи для должника
     * @param $number
     * @return false|string|void
     */
    public static function getFeedbackGuest($number)
    {
        $model = self::findMessageByPhone($number);

        if ($model != null) {

            return json_encode([
                'feedback' => $model->feedback
            ]);
        }
    }

    /** Возвращает json для атс, с типом сообщения должнику
     * сумму долга, участок по условию
     * @param $number
     * @return false|string|void
     */
    public static function getMessage($number)
    {
        $model = self::findMessageByPhone($number);

        if ($model != null){
            $debtor = Debtor::findDebtor($model->list_debtor_id);
            $list = ListOfDebtor::findNumber($number);
            $regions = Region::findListDebtor($model->list_debtor_id);

            if(is_array($regions)){
                foreach ($regions as $value){
                    if ($value->region_id){
                        $region[] = $value->region_id;
                    }
                }
            }

            $message = [
                'template_id' => $model->type_scenary,
                'credit' => $debtor->credit,
                'action' => $model->feedback,
            ];

            if ($region != null) {
                $message['regions'] = $region;
            }
            if ($model->type_scenary == 0){
                $message['date_sound'] = $list->date_sound;
            }


            return json_encode($message);
        }
    }
}
