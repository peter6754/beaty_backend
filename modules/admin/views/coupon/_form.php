<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Coupon $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="coupon-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->image_path && file_exists(Yii::getAlias('@webroot/').$model->image_path)) : ?>
        <div class="current-image">
            <label>Текущее изображение:</label><br>
            <img src="<?= $model->getImageUrl() ?>" style="max-width: 200px; max-height: 150px;"><br><br>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <?= $form->field($model, 'category_id')->widget(Select2::classname(), ['data' => $category]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>