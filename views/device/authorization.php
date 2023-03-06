<?php $form = \yii\bootstrap5\ActiveForm::begin()?>

<?= $form->field($device, 'id_device')
    ->textInput()
    ->label() ?>

<?php \yii\bootstrap5\ActiveForm::end();?>
