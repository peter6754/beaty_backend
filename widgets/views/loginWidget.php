<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
?>

<?php Modal::begin([
    'title'=>'Войти',
    'id'=>'login-modal',
]);
?>

    <div class="container">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'enableAjaxValidation' => true,
            'action' => ['site/login']
        ]);?>

            <?= $form->field($model, 'phone')->textInput(['class' => 'form-control phone_mask']) ?>

            <?=$form->field($model, 'password', ['enableAjaxValidation' => true])->passwordInput();?>

            <?=$form->field($model, 'rememberMe')->checkbox();?>

            <div class="row">
                <div class="col-6">
                    <?= Html::a('Зарегистрироваться', '#', ['data-bs-toggle' => 'modal', 'data-bs-target' => '#register-modal']) ?>
                </div>
                <div class="col-6">
                    <?= Html::a('Забыли пароль?', '#', ['data-bs-toggle' => 'modal', 'data-bs-target' => '#forgot-modal']) ?>
                </div>
            </div>
            <div class="form-group">
                <div class="text-center">

                    <?=Html::submitButton('Войти', ['class' => 'btn btn-primary auth-button', 'name' => 'login-button']); ?>

                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php Modal::end(); ?>