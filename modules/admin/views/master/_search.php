<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MasterSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'balance') ?>

    <?= $form->field($model, 'lastname') ?>

    <?= $form->field($model, 'firstname') ?>

    <?php // echo $form->field($model, 'middlename') ?>

    <?php // echo $form->field($model, 'gender') ?>

    <?php // echo $form->field($model, 'birthday') ?>

    <?php // echo $form->field($model, 'work_city') ?>

    <?php // echo $form->field($model, 'work_street') ?>

    <?php // echo $form->field($model, 'work_house') ?>

    <?php // echo $form->field($model, 'work_lat') ?>

    <?php // echo $form->field($model, 'work_lon') ?>

    <?php // echo $form->field($model, 'live_city') ?>

    <?php // echo $form->field($model, 'live_street') ?>

    <?php // echo $form->field($model, 'live_house') ?>

    <?php // echo $form->field($model, 'live_lat') ?>

    <?php // echo $form->field($model, 'live_lon') ?>

    <?php // echo $form->field($model, 'search_radius') ?>

    <?php // echo $form->field($model, 'client_gender') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
