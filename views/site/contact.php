<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Beautyms';
?>

<div class="banner">
    <div class="container">
        <div class="block-title">
            <h2>контакты</h2>
            <h1>Связаться<br/>с нами!</h1>
            <div class="buttons">
                <a href="#order" class="btn btn-lg btn-primary">Подать заявку на услугу</a>
                <a href="<?= Url::to(['category/index'])?>" class="btn btn-lg btn-outline-primary">Выбор услуги</a>
            </div>
        </div>
    </div>
    <?=app\components\backgroundWidget::widget(["type" => "contact"])?>
</div>


<div class="contacts">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="contact_block">
                    <img src="/images/icons/icon_mail.svg" />
                    <h4>e-mail</h4>
                    <h5>beauty.master.sdelka@yandex.ru</h5>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="contact_block">
                    <img src="/images/icons/icon_phone.svg" />
                    <h4>Телефон</h4>
                    <h5>+7 (926) 756 44 44</h5>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="contact_block">
                    <img src="/images/icons/icon_contact_work.svg" />
                    <h4>График работы</h4>
                    <h5>Пн-Вс 08:00-22:00</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<?=app\widgets\OrderWidget::widget()?>
