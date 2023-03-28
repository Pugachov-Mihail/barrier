<?php

namespace app\models;

use yii\data\ActiveDataProvider;
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

    public static $typeStatusAdd = [
         0 => "Синхронизирован",
         1 => "Данные неактуальны",
         2 =>  "Локальные изменения не отправлены",
         3 => "Происходит обновление",
         4 => "Локально добавлен",
    ];

    /**
     * Как добавлен пользователь, локально или получен с апи
     * @var int[]
     */
    public static $typeAdd = [
         0 => "Добавлен из INOM",
         4 => "Добавлен в ручную"
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
     * Поиск номера телефона в списке посетителей
     * @param $number
     * @return array|ActiveRecord|null
     */
    public static function findNumber($number)
    {
        if(!self::validateNumber($number)){
            return self::find()
                ->where(['=', 'phone', $number])
                ->one();
        }
        return null;
    }

    /**
     * Сохранение полученных данных от апи Ином
     * @throws \Throwable
     * @throws Exception
     */
    public function add($values, $token)
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
        $company_name = property_exists($values, "company_name") ? $values->company_name : null;

        $model->self_id = property_exists($values, "self_id") ? $values->self_id : null;
        $model->type_user = property_exists($values, "type_user") ? $values->type_user : null;
        $model->phone = $phone;
        $model->open_gate = property_exists($values, "open_gate") ? $values->open_gate : null;
        $model->inom_id = $inom_id;
        $model->created_at = time();
        $model->type_sync = 0;

        $device->saveResult($company_id, $token, $company_name);

        $insert = $model->insert();

        $model->saveDebtor($inom_id, $debtor, $type_pattern, $type_action, $credit, $phone, $company_id, $company_name);
        Region::saveRegion($accounts, $model->id, $inom_id);

        if (!$insert) {
            throw new Exception("Ошибка сохранения данных");
        }
    }

    /**
     * Сохранение списка с долгами посетителей
     * @param $inom_id
     * @param $debtors
     * @param $type_pattern
     * @param $type_action
     * @param $credit
     * @param $phone
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    private function saveDebtor($inom_id, $debtors, $type_pattern, $type_action, $credit, $phone, $company_id, $company_name)
    {
        $debtor = new Debtor();
        $message_for_debter = new MessageForDebtor();

        if ($inom_id != null ) {
            $debtor->list_debtor_id = $message_for_debter->list_debtor_id = $this->id;
            $debtor->inom_id = $message_for_debter->inom_id = $inom_id;

            if ($debtors != null || $debtors == 0) {
                $debtor->debt = $debtors;
            }
            if ($company_id != null || $company_id == 0 && $company_name != null || $company_name == 0){
                $message_for_debter->company_id = $company_id;
                $message_for_debter->company_name = $company_name;
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
            if ($credit != null || $credit == 0){
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

    /**
     * Возвращает из шаблона форматированный номер телефона
     * @param $phone
     * @return array|string|string[]|null
     */
    public static function viewFormPhone($phone)
    {
        $res = preg_replace(
            array(
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',
            ),
            array(
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4',
                '+7 ($2) $3-$4',
            ),
            $phone
        );

//        $res = str_replace(' ', '&nbsp', $res);

        return $res != "" ? $res : $phone;

    }

    /**
     * Провайдер данных для списка посетителей
     * @return ActiveDataProvider
     */
    public function dataProviderDebtorList()
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'forcePageParam' => false,
                'pageSizeParam' => false,
                'pageSize' => 25
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Поиск должника по номеру телефона
     * @param $phone
     * @return array|ActiveRecord|null
     */
    public static function findDebtor($phone)
    {
        $model = self::findNumber($phone);
        if ($model != null) {
            $debtor = Debtor::find()
                ->where(['=', 'id', $model->id])
                ->one();
        }

        return !is_object($debtor) ? $debtor : null;
    }

    /**
     * Проверка должника, открываем ему шлагбаум или нет.
     * Если должник возвращает true
     * @param $phone
     * @return bool|void
     */
    public function getDebtor($phone)
    {
        $modelDebtor = self::findDebtor($phone);
        if ($modelDebtor->debt == 0){
            return true;
        } else {
            return false;
        }
    }
}