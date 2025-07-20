<?php

use yii\helpers\Html;

$this->title = 'Оплата прошла успешно';
?>

<div class="payment-success">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="alert alert-success">
                    <h1><?= Html::encode($this->title) ?></h1>
                    <p>Ваш платеж успешно обработан.</p>
                    <p>Спасибо за покупку!</p>
                    <?= Html::a('Вернуться на главную', ['site/index'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>