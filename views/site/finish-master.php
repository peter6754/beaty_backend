<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Beautyms';
?>

<div class="banner">
    <div class="container">
        <div class="block-title">
            <h1>Данные приняты<br/>для проверки и<br/>регистрации!</h1>
            <h3>Ожидайте, мы Вас оповестим.</span></h3>
        </div>
    </div>
    <?=app\components\backgroundWidget::widget()?>
</div>
