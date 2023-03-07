<?php

namespace app\models;

use yii\db\ActiveRecord;

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

    public static function checkDebtor()
    {

    }
}