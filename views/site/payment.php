<?php

/** @var yii\web\View $this */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Payment';

?>

<div class="banner">
    <div class="container">
        <div class="block-title">
            <h2>оплата</h2>
            <h1>Как оплатить и<br/>воспользоваться<br/>услугой!</h1>
            <div class="buttons">
                <a href="#order" class="btn btn-lg btn-primary">Подать заявку на услугу</a>
                <a href="<?= Url::to(['category/index'])?>" class="btn btn-lg btn-outline-primary">Выбор услуги</a>
            </div>
        </div>
    </div>
    <?=app\components\backgroundWidget::widget(["type" => "payment"])?>
</div>

<div class="container">
    <div class="site-about">
        <h1><?= Html::encode($this->title) ?></h1>

        <code><?= __FILE__ ?></code>
    </div>
</div>

<?=app\widgets\OrderWidget::widget()?>