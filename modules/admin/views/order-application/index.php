<?php

use app\models\OrderApplication;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\OrderApplicationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Order Applications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-application-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            //'user_id',
            //'order_coupon_id',
            'price',
            'name',
            'phone',
            'date',
            'time',
            //'city',
            //'street',
            //'house',
            //'apartment',
            //'entrance',
            //'floor',
            //'intercom',
            //'lat',
            //'lon',
            //'product_id',
            //'comment:ntext',
            //'master_id',
            //'status',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
