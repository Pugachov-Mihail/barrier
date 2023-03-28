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
     * Функция для отправки логина и пароля, возвращает токен либо json с ошибкой
     * @param $login
     * @param $password
     * @return mixed
     */
    public function getTokenAuth($login, $password)
    {
        $url = 'http://127.0.0.2:8000/login';

        $data = [
            'login'  => $login,
            'password' => $password
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        if (is_bool($res)){
            return $res;
        }

        $token_or_error = json_decode($res);
        $result = property_exists($token_or_error, "token") ? $token_or_error->token : true;

        if (is_bool($result)){
            return true;
        } else {
            return self::saveLoginAndPass($login, $password, $result);
        }
    }

    public static function authConnectionGetDataDebtor($token)
    {
        if(is_bool($token)){
            return false;
        } else {
            $data = Device::getInfo($token);
            if (is_bool($data)){
                return false;
            } else {
                return Device::saveReceived($data, $token);
            }
        }
    }

    /** Получение всех разрешенных для проезда посетителей
     * @param $token
     * @return false|array
     */
    public static function getInfo($token)
    {
        $url = 'http://127.0.0.2:8000/all';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        if(is_bool($res)){
            return $res;
        }

        $guest = json_decode($res);

        return is_array($guest) ? $guest : false;
    }

    public static function saveReceived($data, $token)
    {
        $list_debtor = new ListOfDebtor();

        if( $data != null){
            foreach ($data as $values) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $list_debtor->add($values, $token);

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
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
    public function saveResult($company_id, $token, $company_name)
    {
        $access_token = AccessToken::find()
            ->where(['=', 'token', $token])
            ->one();
        $model = self::find()
            ->where(['=', 'id', $access_token->id_device])
            ->one();

        if ($model->findCompany($company_id)){
            $model->updateAttributes([
               'company_id' =>  $company_id,
                'company_name' => $company_name,
            ]);

            $model->insert();
        }
        return $model != null ? $model : null;
    }

    public static function saveDevice($login, $pass, $token)
    {
        $model = new self();

        $model->login = $login;
        $model->password = $pass;
        $model->created_at = time();

        if ($model->save()){
            return $model->saveAuthToken($token);
        } else {
            return false;
        }
    }

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


    public static function findDevice($login)
    {
        $model = self::find()
            ->where(['=', 'login', $login])
            ->one();
        return $model != null ? $model : null;
    }

    private function checkPassword($password)
    {
        return $password == $this->password;
    }

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

    public static function findAccessToken($token)
    {
        $model = AccessToken::find()
            ->where(['=', 'token', $token])
            ->all();
        return !(count($model) >= 1);
    }

    public function findCompany($company_id)
    {
        $model = self::find()->where(['=', 'company_id', $company_id])->all();
        if (count($model) >= 1){
            return false;
        }
        return true;
    }

    /**
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

    public static function sendJournal($data)
    {
        $url = 'http://127.0.0.1:8000/send-data';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $response = is_bool($res) ? false : json_decode($res);

        return is_bool($response) ? false : $response->status;
    }
}