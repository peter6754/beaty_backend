<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap5\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
    
            'id',
            [
                'attribute' => 'active',
                'content' => function ($data) {
                        return $data->active ? '<i class="fas fa-check"></i>' : '<i class="fas fa-ban"></i>';
                    }
            ],
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>