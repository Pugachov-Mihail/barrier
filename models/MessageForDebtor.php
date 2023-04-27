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

    private static function getRegion($msg)
    {
        $value = $msg;
        if (preg_match('/\//', $msg)){
            $value = preg_replace('/\//', ' дробь ' , $msg );
        }
        if (preg_match('/-/', $msg)){
            $value = preg_replace('/-/', ' дефис ', $msg);
        }
        if (preg_match('/\,/', $msg)){
            $value = preg_replace('/\,/', '', $msg);
        }
        if (preg_match('/\./', $msg)){
            $value = preg_replace('/\./', ' точка ', $msg);
        }

        return $value;
    }

    private static function findSpace($msg)
    {
        $value = $msg;
        if (preg_match('/\s/', $msg)){
            $value = preg_replace('/\s/', ' пробел ', $msg);
        }
        return $value;
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
                        $region[] = self::getRegion(self::findSpace($value->region_id));
                    }
                }
            }

            return self::getMessageForGuest($model, $debtor, $region, $list);
        }
    }

    private static function getMessageForGuest($model, $debtor, $region, $list)
    {

        $message = [
            'template_id' => $model->type_scenary,
        ];

        if ($region != null && $model->type_scenary > 3) {
            $message['regions'] = implode(" пробел ", $region);
            $message['credit'] = $debtor->credit;
        } else {
            $message['credit'] = $debtor->credit;
        }

        if ($model->type_scenary == 0){
            $message['date_sound'] = $list->date_sound;
        }

        $message['action'] = $model->feedback;

        return json_encode($message, JSON_UNESCAPED_UNICODE);
    }
}
//337/ЮЗ лин
//261 1/2(261 пробел 1/2)
//219а
//176/1
//166, 167
//138/ИГ
// 134-2
//107а/ИГ