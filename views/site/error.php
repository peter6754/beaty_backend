<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $name;
?>

<div class="error container">
    <div class="row">
        <div class="col-md-6 order-md-1 order-2 col-12">
            <img src="<?= Url::to(['/images/404.png'])?>" />
        </div>
        <div class="col-md-6 order-md-1 order-1 col-12">
            <div class="error-info">
                <h1>Упс!</h1>
                <h3><?= nl2br(Html::encode($message)) ?></h3>
                <p>Пожалуйста, свяжитесь с нами, если вы считаете, что это ошибка сервера. Благодарю вас.</p>
            </div>
        </div>
    </div>
</div>

<?=app\widgets\OrderWidget::widget()?>