<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'active')->checkbox() ?>

    <?php if ($model->image_path && file_exists(Yii::getAlias('@webroot/').$model->image_path)) : ?>
        <div class="current-image">
            <label>Текущее изображение:</label><br>
            <img src="<?= Yii::$app->request->baseUrl.'/'.$model->image_path ?>"
                style="max-width: 200px; max-height: 150px;"><br><br>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'color')->widget(ColorInput::classname(), [
        'options' => ['placeholder' => 'Select color ...'],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>