<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
?>

<?php Modal::begin([
    'title'=>'Восстановить пароль',
    'id'=>'forgot-modal',
]);
?>

    <div class="container">
        <?php $form = ActiveForm::begin([
            'id' => 'forgot-form',
            'enableAjaxValidation' => true,
            'action' => ['site/forgot']
        ]);?>

        <?=$form->field($model, 'email')->textInput();?>

        <div class="form-group">
            <div class="text-center">

                <?=Html::submitButton('Восстановить', ['class' => 'btn btn-primary auth-button', 'name' => 'forgot-button']); ?>

            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php Modal::end(); ?>