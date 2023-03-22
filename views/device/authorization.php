<?php
/** @var $device*/

use yii\helpers\Html;

?>

<?php if(Yii::$app->session->hasFlash('danger')): ?>
    <div class="alert text-danger box-danger-bg" role="alert">
    </div>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('success')): ?>
    <div class="alert text-success box-success-bg" role="alert">
    </div>
<?php endif; ?>


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

<div class="form-group">
    <?= Html::submitButton('Авторизовать', ['class' => 'btn btn-primary']) ?>
</div>

<?php \yii\bootstrap5\ActiveForm::end();?>
