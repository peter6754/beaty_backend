<?php

use app\models\Master;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\MasterSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => 'app\components\BootstrapLinkPager',
        ],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute'=> 'user_id',
                'content'=>function($data) {
                    return isset($data->user) ? $data->user->phone : null;
                }
            ],
            'lastname',
            'firstname',
            'middlename',
            'balance',
            'date:datetime',
            //'gender',
            //'birthday:date',
            //'work_city',
            //'work_street',
            //'work_house',
            //'work_lat',
            //'work_lon',
            //'live_city',
            //'live_street',
            //'live_house',
            //'live_lat',
            //'live_lon',
            //'search_radius',
            //'client_gender',
            [
                'filter' => Yii::$app->params["master_status"],
                'attribute'=> 'status',
                'content'=>function($data) {
                    return Yii::$app->params["master_status"][$data->status];
                }
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
