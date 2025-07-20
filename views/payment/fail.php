<?php

use yii\helpers\Html;

$this->title = 'Ошибка оплаты';
?>

<div class="payment-fail">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="alert alert-danger">
                    <h1><?= Html::encode($this->title) ?></h1>
                    <p>Произошла ошибка при обработке платежа.</p>
                    <p>Попробуйте еще раз или обратитесь в службу поддержки.</p>
                    <?= Html::a('Попробовать снова', ['site/index'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>