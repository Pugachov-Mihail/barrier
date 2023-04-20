<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Устройство
 * @property int $id
 * @property int $id_device id устройства
 * @property int $company_id компания
 * @property int $company_name название компании
 * @property int $ip_sluice шлюз Апи
 * @property int $login
 * @property int $password
 * @property int $created_at
 * @property int $last_connection
 * @property int $updated_at
 */
class Device extends ActiveRecord
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'password' => 'Пароль',
            'id_device' => 'id устройства',
            'ip_sluice' => 'шлюз Апи',
        ];
    }

    /**
     * Функция для авторизации в ином
     * @param $login
     * @param $password
     * @return mixed
     */
    public function getTokenAuth($login, $password)
    {
       // $url = "http://127.0.0.1:8000/auth";
//        $data = [
//            'login'  => $login,
//            'password' => $password
//        ];

       $url = 'https://api.inom.online/devices/users/login?login='.$login.'&password='. $password;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data')); //array('Content-Type: application/json'));
       //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        if (is_bool($res)){
            \Yii::error("Ошибка авторизации устройства");
            self::sendErrorLogs("Ошибка авторизации устройства");
            return $res;
        }

        $token_or_error = json_decode($res);

        if ($token_or_error->success) {
            foreach ($token_or_error->data as $value) {
                $result = property_exists($value, "token") ? $value->token : true;
            }
        } else {
            return true;
        }

        if (is_bool($result)){
            return true;
        } else {
            // вызывается функция для сохранения логина и пароля
            return self::saveLoginAndPass($login, $password, $result);
        }
    }

    public function authConnectionGetDataDebtor($token)
    {
        if(is_bool($token)){
            return false;
        } else {
            $data = self::getInfo($token);
            if (is_bool($data)){
                return false;
            } else {
                return self::saveReceived($data, $token);
            }
        }
    }

    /** Получение из апи всех разрешенных для проезда посетителей
     * @param $token
     * @return false|array
     */
    public static function getInfo($token)
    {
        $url = 'https://api.inom.online/devices/guests';
        //$url = "http://127.0.0.1:8000/get-all-debtor";

        $headers = [
            'Authorization: Bearer ' . $token,
            //'Authorization:' . $token,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        if(is_bool($res)){
            return $res;
        }

        $guest = json_decode($res);

        if ($guest->success){
            return $guest->data;
        } else {
            \Yii::error("Ошибка получения списка посетителей из апи");
            self::sendErrorLogs("Ошибка получения списка посетителей из апи");
            return false;
        }
    }

    /** Выполняет транзакция с сохранением данных полученных через апи
     * @param $data
     * @param $token
     * @return string
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public static function saveReceived($data, $token)
    {
        $list_debtor = new ListOfDebtor();

        if( $data != null){
            foreach ($data as $key=>$values) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($key == array_key_first($data)){
                        ListOfDebtor::deletePhone($data);
                        self::firstIteration($values, $token);
                        $transaction->commit();
                    }
                    if ($list_debtor->add($values)){
                        $transaction->commit();
                    } else {
                        continue;
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    \Yii::error("Ошибка сохранения данных");
                    self::sendErrorLogs("Ошибка сохранения данных");
                    throw $e;
                }
            }
            return $token;
        }
        return "Сохранено";
    }


    /**
     * @throws \Throwable
     */
    public static function saveResult($company_id, $token, $company_name)
    {
        $access_token = AccessToken::find()
            ->where(['=', 'token', $token])
            ->one();
        $model = self::find()
            ->where(['=', 'id', $access_token->id_device])
            ->one();

        if ($model->findCompany($company_id, $company_name)){
            $model->updateAttributes([
               'company_id' =>  $company_id,
                'company_name' => $company_name,
            ]);
        }
        return $model != null ? $model : null;
    }

    public static function saveDevice($login, $pass, $token)
    {
        if (self::findDeviceCreate($login, $pass) == null) {
            $model = new self();

            $model->login = $login;
            $model->password = $pass;
            $model->created_at = time();

            if ($model->save()) {
                return $model->saveAuthToken($token);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private static function findDeviceCreate($login, $pass)
    {
        $model = self::find()
            ->where(['=', 'login', $login])
            ->andWhere(['=', 'password', $pass])
            ->one();

        return $model != null ? $model : null;
    }

    /** Функция для сохранения логина и пароля.
     * проверяет есть ли логин в базе, проверяет пароль
     * и проверяет токен для этого устройства,
     * возвращает токен устройства
     * @param $login
     * @param $pass
     * @param $token
     * @return false
     */
    public static function saveLoginAndPass($login, $pass, $token)
    {
        if (!empty($login) && !empty($pass)) {
            $find_login = self::findDevice($login);

            if (is_object($find_login)){
                if ($find_login->checkPassword($pass)){
                    if (!self::findAccessToken($token)){
                        return $token;
                    } else {
                        $find_login->saveAuthToken($token);
                        return $token;
                    }
                } else {
                    $find_login->updateAttributes([
                        'password' => $pass,
                    ]);
                    return $token;
                }
            } else {
                self::saveDevice($login, $pass, $token);
                return $token;
            }
        } else {
            return false;
        }
    }

    /** поиск устройства по логину
     * @param $login
     * @return array|ActiveRecord|null
     */
    public static function findDevice($login)
    {
        $model = self::find()
            ->where(['=', 'login', $login])
            ->one();
        return $model != null ? $model : null;
    }

    /** Проверка пароля
     * @param $password
     * @return bool
     */
    private function checkPassword($password)
    {
        return $password == $this->password;
    }

    /** Создание и сохранение токена авторизации
     * @param $token
     * @return AccessToken|false
     */
    public function saveAuthToken($token)
    {
        if (self::findAccessToken($token)){
            $access_token = new AccessToken();

            $access_token->token = $token;
            $access_token->created_at = $access_token->created = time();
            $access_token->id_device = $this->id;

            if ($access_token->save()){
                return $access_token;
            } else {
                return false;
            }
        } return $token;
    }

    /** поиск текущего токена на его существование
     * @param $token
     * @return bool
     */
    public static function findAccessToken($token)
    {
        $model = AccessToken::find()
            ->where(['=', 'token', $token])
            ->all();
        return !(count($model) >= 1);
    }

    /** Проверяет на существование такой компании на устройстве
     * @param $company_id
     * @param $company_name
     * @return bool
     */
    public function findCompany($company_id, $company_name)
    {
        $model = self::find()
            ->where(['=', 'company_id', $company_id])
            ->andWhere(['=', 'company_name', $company_name])
            ->all();

        if (count($model) >= 1){
            return false;
        }
        return true;
    }

    /** ищет по токену в базе и возвращает модель устройства
     * @param $token
     * @return array|false|ActiveRecord | bool
     */
    public static function deviceModelFindOnToken($token)
    {
        $id_device = AccessToken::findToken($token);
        $model = self::find()
            ->where(['=', 'id', $id_device->id_device])
            ->one();

        return $model != null ? $model : false;
    }

    /** обновляет последнее соединение с ином
     * @param $token
     * @return array|bool|ActiveRecord
     */
    public static function updateLastConnection($token)
    {
        if (is_bool($token)){
            return false;
        }

        $model = self::deviceModelFindOnToken($token);

        $model->updateAttributes([
           'last_connection' => time()
        ]);

        if ($model->save()){
            return $model;
        }
        return false;
    }

    /** поиск по айди компании и возвращает последнюю запись устройства
     * @param $company_id
     * @return array|ActiveRecord|null
     */
    public static function findDeviceOnSend($company_id)
    {
        return self::find()
            ->orderBy('id desc')
            ->limit(1)
            ->one();
    }

    /** Отправка журнала посещений на апи ином
     * @param $data
     * @param $token
     * @return bool
     */
    public static function sendJournal($data, $token)
    {
       if (!empty($data)){
           $url = 'https://api.inom.online/devices/guests';
           //$url = 'http://127.0.0.1:8000/dasddassdasadfdsfggfgfd';

           $ch = curl_init($url);

           $headers = [
               'Content-Type: multipart/form-data',
               'Authorization: Bearer ' . $token,
               // 'Authorization: ' . $token,
           ];

           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
           curl_setopt($ch, CURLOPT_HEADER, false);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

           $res = curl_exec($ch);
           curl_close($ch);

           if(is_bool($res)){
               return $res;
           }

           $response = json_decode($res);

           if ($response->success){
               return $response->success;
           } else {
               \Yii::error("Ошибка отправки журнала посещений");
               self::sendErrorLogs("Ошибка отправки журнала посещений");
               return false;
           }
       } else {
           return false;
       }
    }

    /** Сохранение для устройства компании при получении данных от ином
     * @param $values
     * @param $token
     * @return void
     * @throws \Throwable
     */
    public static function firstIteration($values, $token)
    {
        $company_id = property_exists($values, "company_id") ? $values->company_id : null;
        $company_name = property_exists($values, "company_name") ? $values->company_name : null;

        self::saveResult($company_id, $token, $company_name);
    }

    /** Проверка есть ли токен
     * @param $pages
     * @return bool
     */
    public static function findPages($pages)
    {
        if ($pages == null){
            return true;
        } else {
            return false;
        }
    }

    public static function sendErrorLogs($message)
    {
        $url = 'http://127.0.0.1:8000/send-data';

        $ch = curl_init($url);

        $data = [
            'message' => $message,
        ];

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        if(is_bool($res)){
            return $res;
        }

        $response = json_decode($res);

        if ($response->success){
            return $response->success;
        } else {
            \Yii::error("Ошибка отправки логов");
            self::sendErrorLogs("Ошибка отправки логов с ошибкой");
            return false;
        }
    }

}