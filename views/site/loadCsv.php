<?php
use yii\widgets\ActiveForm;

?>
<div class="mt-5">
    <section>
        <h2>Инструкция по добавлению разрешенных номеров</h2>
        <p>
            Чтобы, добавить в базу новые разрешенные номера.
            Необходимо загрузить файл в формате <b>csv</b>, можно сделать в Exel.
            В качестве разделителя необходимо выбрать - <b>;</b>
        </p>
    </section>
    <?php
    $form = ActiveForm::begin(['options' =>['enctype' => 'multipart/form-data']]);
    ?>
    <?= $form->field($model, 'csvFile')->fileInput()?>
    <button class="mt-4 btn btn-sm btn-success">Загрузить</button>
    <?php ActiveForm::end()?>
</div>

