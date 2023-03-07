<?php
/** @var $device*/
/** @var $token */

use yii\helpers\Html;

?>

<?php $form = \yii\bootstrap5\ActiveForm::begin()?>
<h2>Авторизация устройства</h2>
<div class="row">
    <div class="col-lg-5">
    <?= $form->field($device, 'login')
        ->textInput()
        ->label() ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <?= $form->field($device, 'password')
            ->passwordInput()
            ->label() ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <?= Html::encode($token)?>
    </div>
</div>


<div class="form-group">
    <?= Html::submitButton('Авторизовать', ['class' => 'btn btn-primary']) ?>
</div>

<?php \yii\bootstrap5\ActiveForm::end();?>
