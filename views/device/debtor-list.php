<?php
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\grid\GridView;


?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{pager}\n{summary}\n{items}\n{pager}",
    'columns' => [

        [
            'label' => "ID",
            'value' => function($row) {
                return $row->id ? $row->id : "--";
            }
        ],
        [
            'label' => "Телефон",
            'value' => function($row) {
                return $row->phone ? \app\models\ListOfDebtor::viewFormPhone($row->phone) : "--";
            }
        ],
        [
            'label' => "Тип посетителя",
            'value' => function($row) {
                return $row->type_user >= 0 ? \app\models\ListOfDebtor::$typeUser[$row->type_user] : "--";
            }
        ],
        [
            'label' => "Сумма долга",
            'value' => function($row) {
                return $row->id >= 0 ? \app\models\Debtor::findCredit($row->id) : "--";
            }
        ],
        [
            'label' => "Номер участка",
            'value' => function($row) {
                return $row->inom_id != null ? \app\models\Region::returnRegion($row->inom_id) : "--";
            }
        ],
        [
            'label' => "Тип добавления жителя",
            'value' => function($row) {
                return $row->type_sync >= 0 ? \app\models\ListOfDebtor::$typeAdd[$row->type_sync] : "--";
            }
        ],
        [
            'label' => "Статус синхронизации",
            'value' => function($row) {
                return $row->type_sync >= 0 ? \app\models\ListOfDebtor::$typeStatusAdd[$row->type_sync] : "--";
            }
        ],
        [
            'label' => "Название компании",
            'value' => function($row) {
                return $row->id >= 0  ? \app\models\MessageForDebtor::findCompanyName($row->id) : "--";
            }
        ],
    ],
]); ?>
