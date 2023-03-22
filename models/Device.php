<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * Устройство
 * @property int $id
 * @property int $id_device id устройства
 * @property int $company_id компания
 * @property int $ip_sluice шлюз Апи
 * @property int $login
 * @property int $password
 * @property int $created_at
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
    public static function sendLoginAndPassword($login, $password)
    {
        $url = 'http://127.0.0.1:8000/login';

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
        $url = 'http://127.0.0.1:8000/all';

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

        $guest = json_decode($res);

        return is_array($guest) ? $guest : false;
    }

    public static function saveReceived($data, $token)
    {
        if( $data != null){
            foreach ($data as $values) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    ListOfDebtor::add($values, $token);
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

    public function saveResult($company_id, $token)
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
            ]);

            $model->insert();
        }
        return $model;
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
            $findLogin = self::findDevice($login);
            if (is_object($findLogin)){
                if ($findLogin->checkPassword($pass)){
                    if (!self::findAccessToken($token)){
                        return $token;
                    } else {
                        $findLogin->saveAuthToken($token);
                        return $token;
                    }
                } else {
                    $findLogin->updateAttributes([
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
        return self::find()
            ->where(['=', 'login', $login])
            ->one();
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
}