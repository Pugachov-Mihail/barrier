<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;
use app\models\Debtor;
use app\models\MessageForDebtor;

/**
 * Model List of debtor
 *
 * @property int $id
 * @property int $inom_id id физю лица в inom
 * @property string $lastname
 * @property string $firstname
 * @property string $middlename
 * @property string $phone номер телефона
 * @property int $type_user тип пользователя
 * @property int $status статус синхронизации
 * @property int $type_sync как добавлена запись, вручную или из апи
 * @property int $self_id родительский идентификатор физ.лица
 * @property int $open_gate открытие шлагбаума
 * @property int $created_at дата добавления записи
 */
class ListOfDebtor extends ActiveRecord
{
    public static $typeUser = [
        0 => "Житель",
        1 => "Сотрудник",
        2 => "Посетитель"
    ];

    public static $openGate = [
        0 => "Не открывать",
        1 => "Открывать"
    ];

    public function attributeLabels()
    {
        return [
            'id' => "ID",
            'inom_id' => "ID Физ. лица",
            'lastname' => "Фамилия",
            'firstname' => "Имя",
            'middlename' => "Отчество",
            'phone' => "Телефон",
            'type_user' => "Тип посетителя",
            'status' => "Статус синхронизации",
            'type_sync' => "Признак добавления",
            'self_id' => "Кто добавил",
            'open_gate' => "Открытие шлагбаума",
            'created_at' => "Дата добавления записи"
        ];
    }

    /**
     * Сохранение отчета об открытии шлагбаума
     * @param $model
     * @return bool
     */
    public function saveOpenGate($model)
    {
        $model->open_gate = 1;
        $model->created_at = time();
        if($model->save()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Сохранение отчета о не открытии шлагбаума
     * @param $model
     * @return bool
     */
    public function saveDontOpenGate($model)
    {
        $model->open_gate = 0;
        $model->created_at = time();
        if($model->save()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверка номера на корректность, если номер соответствует условию то возвращает false
     * @param $number
     * @return bool
     */
    public static function validateNumber($number)
    {
        if($result = preg_match("/[0-9]{0,11}/", $number)) {
            if ((strlen($number)) > 12 || (strlen($number) < 11)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $number
     * @return array|ActiveRecord|null
     */
    public static function findUser($number)
    {
        if(!self::validateNumber($number)){
            return self::find()
                ->where(['=', 'number' => $number])
                ->one();
        }
        return null;
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    public static function add($values, $token)
    {
        $model = new self();
        $device = new Device();

        $accounts = property_exists($values, "accounts") ? $values->accounts : null;
        $inom_id = property_exists($values, "inom_id") ? $values->inom_id : null;
        $debtor = property_exists($values, "debtor") ? $values->debtor : null;
        $type_pattern = property_exists($values, "type_pattern") ? $values->type_pattern : null;
        $type_action = property_exists($values, "type_action") ? $values->type_action : null;
        $credit = property_exists($values, "credit") ? $values->credit : null;
        $phone = property_exists($values, "phone") ? $values->phone : null;
        $company_id = property_exists($values, "company_id") ? $values->company_id : null;

        Region::saveRegion($accounts);

        $model->self_id = property_exists($values, "self_id") ? $values->self_id : null;
        $model->type_user = property_exists($values, "type_user") ? $values->type_user : null;
        $model->phone = $phone;
        $model->open_gate = property_exists($values, "open_gate") ? $values->open_gate : null;
        $model->inom_id = $inom_id;
        $model->created_at = time();

        $device->saveResult($company_id, $token);

        $model->saveDebtor($inom_id, $debtor, $type_pattern, $type_action, $credit, $phone);

        $insert = $model->insert();

        if (!$insert) {
            throw new Exception("Ошибка сохранения данных");
        }
    }


    private function saveDebtor($inom_id, $debtors, $type_pattern, $type_action, $credit, $phone)
    {
        $debtor = new Debtor();
        $message_for_debter = new MessageForDebtor();


        if ($inom_id != null ) {
            $debtor->inom_id = $inom_id;
            $message_for_debter->id_inom = $inom_id;

            if ($debtors != null || $debtors == 0) {
                $debtor->debt = $debtors;
            }
            if ($type_pattern != null || $type_pattern == 0) {
                $message_for_debter->type_scenary = $type_pattern;
            }
            if ($type_action != null || $type_action == 0) {
                $message_for_debter->feedback = $type_action;
            }
            if ($phone != null) {
                $message_for_debter->phone = $phone;
                $message_for_debter->create_at = time();
            }
            if ($credit != null){
                $debtor->credit = $credit;
            }

            $inser_debtor = $debtor->insert();
            $insert_message = $message_for_debter->insert();

            if (!$inser_debtor && !$insert_message) {
                throw new Exception("Ошибка сохранения данных");
            } else {
                return true;
            }
        }
        return false;
    }


}