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

    /** Убирает символы и вместо них пишет что за символ
     * @param $msg
     * @return array|string|string[]|null
     */
    private static function getRegion($msg)
    {
        $value = $msg;
        if (preg_match('/\//', $msg)){
            $value = preg_replace('/\//', ' дробь ' , $msg );
        }
        if (preg_match('/-/', $msg)){
            $value = preg_replace('/-/', ' тире ', $msg);
        }
        if (preg_match('/\,/', $msg)){
            $value = preg_replace('/\,/', '', $msg);
        }
        if (preg_match('/\./', $msg)){
            $value = preg_replace('/\./', ' точка ', $msg);
        }
        if (preg_match('/\;/', $msg)){
            $value = explode(';', $msg); //preg_replace('/\;/', ' ', $msg);
        }

        return $value;
    }

    /** Убирает пробел, и вместо него пишет слово: "пробел"
     * @param $msg
     * @return array|string|string[]|null
     */
    private static function findSpace($msg)
    {
        $value = $msg;
        if (preg_match('/\s/', $msg)){
            $value = preg_replace('/\s/', ' пробел ', $msg);
        }
        return $value;
    }

    private static function addSpace($str)
    {
//        $re = self::getRegion(self::findSpace($str));

        if (!is_array($str)) {
            foreach (mb_str_split($str) as $char) {
                $a = $char;
                if (!preg_match('/[0-9]/', $char)) {
                    $a = " " . $char . " ";
                }
                $b[] = $a;

            }
            return implode($b);
        } else {
            return $str;
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
                        $space = self::addSpace($value->region_id);
                        $result = self::getRegion($space);
                        if (is_array($result)){
                            $region = $result;
                        } else {
                            $region[] = $result;
                        }
                    }
                }
            }

            if ( is_null($model->type_scenary) || $list->type_user == 1){
                exec("sudo -u www-data sudo python assets/relay_on_1.py");
                return json_encode(["action"=>2]);
            } else {
                return self::getMessageForGuest($model, $debtor, $region, $list);
            }
        }
    }


    /**
     *  Формирует ответ для АТС
     * @param $model
     * @param $debtor
     * @param $region
     * @param $list
     * @return false|string
     */
    private static function getMessageForGuest($model, $debtor, $region, $list)
    {

        $message = [
            'template_id' => $model->type_scenary,
        ];

        if ($region != null && $model->type_scenary > 3 || $model->type_scenary == 8) {
            $message['regions'] = $region;
            $message['credit'] = $debtor->credit;
        } else {
            $message['credit'] = $debtor->credit;
        }

        if ($model->type_scenary == 0){
            $message['date_sound'] = date("Y-m-d", $list->date_sound);
        }
        if ($model->type_scenary < 7) {
            $message['action'] = $model->feedback != null ? $model->feedback : 0;
        }

        return json_encode($message, JSON_UNESCAPED_UNICODE);
    }

    public static function findCompanyName($id)
    {
        $model = self::find()
            ->where(['=', 'list_debtor_id', $id])
            ->one();

        return $model != null ? $model->company_name : "--";
    }
}
