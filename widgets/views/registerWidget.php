<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
?>

<?php Modal::begin([
    'title'=>'Регистрация',
    'id'=>'register-modal',
]);
?>

    <div class="container">
        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'enableAjaxValidation' => true,
            'action' => ['site/register']
        ]);?>

            <?=$form->field($model, 'name')->textInput();?>

            <?= $form->field($model, 'phone')->textInput(['class' => 'form-control phone_mask']) ?>

            <?=$form->field($model, 'email')->textInput();?>

            <?=$form->field($model, 'password')->passwordInput();?>

            <?=$form->field($model, 'password_repeat')->passwordInput();?>

            <div class="form-group">
                <div class="text-center">

                    <?=Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary auth-button', 'name' => 'register-button']); ?>

                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
<?php Modal::end(); ?>