<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
?>

<?php Modal::begin([
    'title' => 'Информация о себе',
    'id' => 'coupon-modal',
]);
?>

<div class="container">
    <?php $form = ActiveForm::begin([
        'id' => 'coupon-form',
        'enableAjaxValidation' => true,
        'action' => ['coupon/register'],
        'options' => [
            'data-pjax' => false
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(); ?>

    <?= $form->field($model, 'phone')->textInput(['class' => 'form-control phone_mask']) ?>

    <?= $form->field($model, 'coupon_id')->hiddenInput()->label(false); ?>

    <div class="form-group">
        <div class="text-center">

            <?= Html::submitButton('Продолжить', ['class' => 'btn btn-primary auth-button', 'name' => 'coupon-button']); ?>

        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Modal::end(); ?>