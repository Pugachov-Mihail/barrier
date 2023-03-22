<?php
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\grid\GridView;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{pager}\n{summary}\n{items}\n{pager}",
    'columns' => [
        [
            'attribute'=>'company_id',
            'value' => function($row) {
                return $row->inom_id;
            }
        ],
    ],
]); ?>
