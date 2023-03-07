<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Устройство
 * @property int $id
 * @property int $id_device id устройства
 * @property int $company_id компания
 * @property int $ip_sluice шлюз Апи
 * @property int $login
 * @property int $password
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
        $url = 'http://127.0.0.1:8000/index';

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
        return $token_or_error->token != null ? $token_or_error->token : $token_or_error ;
    }

    /** Получение всех разрешенных для проезда посетителей
     * @param $token
     * @return string|void
     */
    public static function getInfo($token)
    {
        $listOfDebter = new ListOfDebtor();

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


//        $listOfDebter->inom_id = $guest->inom_id != null ? $guest->inom_id : null;
//        $listOfDebter->lastname = $guest->lastname != null ? $guest->lastname : null;
//        $listOfDebter->firstname = $guest->firstname != null ? $guest->firstname : null;
//        $listOfDebter->middlename = $guest->middlename != null ? $guest->middlename : null;
//        $listOfDebter->phone = $guest->phone != null ? $guest->phone : null;
//        $listOfDebter->type_user = $guest->type_user != null ? $guest->type_user : null;
//        $listOfDebter->type_sync = $guest->type_sync != null ? $guest->type_sync : null;
//        $listOfDebter->self_id = $guest->self_id != null ? $guest->self_id : null;
//        $listOfDebter->open_gate = $guest->open_gate != null ? $guest->open_gate : null;
//        $listOfDebter->created_at = $guest->created_at != null ? $guest->created_at : null;
//        $listOfDebter->status = $guest->status != null ? $guest->status : null;

        if( $guest != null && $listOfDebter->save()){
            foreach ($guest->data as $value) {
                $listOfDebter->attributes = $guest->data;

            }
        } else {
            return "none";
        }

    }


}