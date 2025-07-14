<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Master $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'balance')->textInput() ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'middlename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->textInput() ?>

    <?= $form->field($model, 'birthday')->textInput() ?>

    <?= $form->field($model, 'work_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'work_street')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'work_house')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'work_lat')->textInput() ?>

    <?= $form->field($model, 'work_lon')->textInput() ?>

    <?= $form->field($model, 'live_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'live_street')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'live_house')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'live_lat')->textInput() ?>

    <?= $form->field($model, 'live_lon')->textInput() ?>

    <?= $form->field($model, 'search_radius')->textInput() ?>

    <?= $form->field($model, 'client_gender')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(Yii::$app->params["master_status"]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
