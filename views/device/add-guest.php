<?php
/** @var $guest */
/** @var $region */

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

<h2>Добавить посетителя</h2>

<div class="row">
    <div class="col-lg-5">
        <?= $form->field($guest, 'phone')->label()->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '+7 (999) 999 99 99',
        ])->textInput(['placeholder' => $guest->getAttributeLabel('phone'),]) ?>
    </div>
</div>
<br/>
<div class="row">
    <div class="col-lg-5">
    <?= $form->field($region, 'region_id')
        ->textInput()
        ->label() ?>
    </div>
</div>




<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>

<?php \yii\bootstrap5\ActiveForm::end();?>
