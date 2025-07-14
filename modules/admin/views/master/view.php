<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Master $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="master-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    $files_passport_html = '';
    foreach ($files as $file) {
        if($file->type == 1) {
            $files_passport_html .= '<img width="75" src="' . Url::to("@web/". $file->path) . '"/> ';
        }
    }
    ?>

    <?php
    $files_licenses_html = '';
    foreach ($files as $file) {
        if($file->type == 2) {
            $files_licenses_html .= '<img width="75" src="' . Url::to("@web/". $file->path) . '"/> ';
        }
    }
    ?>

    <?php
    $files_works_html = '';
    foreach ($files as $file) {
        if($file->type == 3) {
            $files_works_html .= '<img width="75" src="' . Url::to("@web/". $file->path) . '"/> ';
        }
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'balance',
            'lastname',
            'firstname',
            'middlename',
            'gender',
            'birthday',
            'work_city',
            'work_street',
            'work_house',
            'work_lat',
            'work_lon',
            'live_city',
            'live_street',
            'live_house',
            'live_lat',
            'live_lon',
            'search_radius',
            'client_gender',
            'status',

            [
                'attribute' => 'Паспорт',
                'value' => $files_passport_html != "" ? $files_passport_html  : null,
                'format' => 'html',
            ],
            [
                'attribute' => 'Лицензия',
                'value' => $files_licenses_html != "" ? $files_licenses_html  : null,
                'format' => 'html',
            ],
            [
                'attribute' => 'Работы',
                'value' => $files_works_html != "" ? $files_works_html  : null,
                'format' => 'html',
            ],
        ],
    ]) ?>

</div>
