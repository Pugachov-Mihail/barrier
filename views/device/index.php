<?php
/** @var $device */
/** @var $journal */
/** @var $status */

$a = Yii::$app->request->get('pages');
use yii\widgets\DetailView;

?>



<div class="container">
    <div class="row">
        <?php if(Yii::$app->session->hasFlash('danger')): ?>
            <div class="alert text-danger box-danger-bg" role="alert">
            </div>
        <?php endif; ?>

        <?php if(Yii::$app->session->hasFlash('success')): ?>
            <div class="alert text-success box-success-bg" role="alert">
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <h3>Характеристики устройства</h3>
    </div>
</div>

<div class="container mb-2">
    <div class="row">
        <div class="col-6">
        </div>
        <div class="col">
            <div class="row justify-content-end">
                <div class="col-auto">
                    <?= \yii\helpers\Html::a("Обновить данные", ['get-debtor-list', 'pages'=>$a],
                        ['class'=>'btn  btn-secondary'] ) ?>
                </div>
                <div class="col-auto">
                    <?= \yii\helpers\Html::a("Авторизация устройства", ['authorization'],
                        ['class'=>'btn btn-success'] ) ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-sm">
            <h4>Информация об оборудовании</h4>
            <?= DetailView::widget([
                    'model' => $device,
                    'attributes' =>[
                        [
                            'label' => 'ID Устройства',
                            'value' => function($row){
                                return $row->id_device ? $row->id_device : "--" ;
                            }
                        ],
                        [
                            'label' => 'Логин',
                            'value' => function($row){
                                return $row->login ? $row->login : "--";
                            }
                        ],
                        [
                            'label' => 'Пароль',
                            'value' => function($row){
                                return $row->password ? $row->password : "--";
                            }
                        ],
                        [
                            'label' => 'Последнее обновление данных с АПИ',
                            'value' => function($row){
                                return $row->last_connection ? date('d.m.Y H:i:s', $row->last_connection) : "--";
                            }
                        ],
                        [
                            'label' => 'Состояние ответа',
                            'value' =>  $status ? "Данные получены" : "Ошибка получения данных"
                        ],
                    ]
             ]);
            ?>
        </div>

        <div class="col-sm">
            <h4>Информация о подключении</h4>
            <?= DetailView::widget([
                    'model' => $journal,
                    'attributes' =>[
                        [
                            'label' => 'Дата отправки',
                            'value' => function($row){
                                return $row->date_send ? date('d.m.Y H:i:s', $row->date_send) : "--";
                            }
                        ],
                        [
                            'label' => 'Статус отправки',
                            'value' => function($row){
                                return $row->response_status ? \app\models\JournalSendData::$statusType[$row->response_status] : "--";
                            }
                        ],
                ]
            ]);
            ?>
        </div>
    </div>
</div>
