<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Model History Barrier
 * @property int $id
 * @property int $phone номер телефона звонившего на шлагбаум
 * @property int $date_open_barrier Дата и время открытия шлагбаума
 * @property int $open_type тип открытия (въезд/выезд)
 * @property int $id_message id сообщения зачитанного звонящему
 * @property int $action действие (открылся / не открылся)
 * @property int $send_in_inom отправленно на сервер Ином
 * @property int $company_id компания
 */
class HistoryBarrier extends ActiveRecord
{

}