<?php
/** @var $device */
/** @var $journal */

use yii\widgets\DetailView;

?>
<div class="row">
    <h3>Характеристики устройства</h3>
</div>

<div class="body-content">
<div class="row">
    <h4>Информация об оборудовании</h4>
    <div class="col-lg-5">
        <?= DetailView::widget([
                'model' => $device,
                'attributes' =>[
                    [
                        'label' => 'ID Устройства',
                        'value' => function($row){
                            return $row->id_device;
                        }
                    ],
                    [
                        'label' => 'Логин',
                        'value' => function($row){
                            return $row->login;
                        }
                    ],
                    [
                        'label' => 'Пароль',
                        'value' => function($row){
                            return $row->password;
                        }
                    ],

                ]
         ]);
        ?>
    </div>
</div>

<div class="row">
    <h4>Информация о подключении</h4>
    <div class="col-lg-5">
        <?= DetailView::widget([
                'model' => $journal,
                'attributes' =>[
                [
                    'label' => 'Последнее обновление данных с АПИ',
                    'value' => function($row){
                        return $row->date_send;
                    }
                ],
                [
                    'label' => 'Состояние ответа',
                    'value' => function($row){
                        return $row->state_response;
                    }
                ],

            ]
        ]);
        ?>
    </div>
</div>
</div>
<p><?= \yii\helpers\Html::a("Авторизация устройства", ['authorization'], ['class'=>'btn btn-lg btn-success'] ) ?></p>
