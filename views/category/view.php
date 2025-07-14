<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $category->name;
?>
    <div class="banner">
        <div class="container">
            <div class="block-title">
                <h2><?= mb_strtoupper($this->title) ?></h2>
                <h1>Бьюти услуги<br/>с выездом к вам!</h1>
                <h3>Купите купон на скидку от 99 рублей<br/>и сэкономьте <span>до 1000 рублей!</span></h3>
                <div class="buttons">
                    <a href="#order" class="btn btn-lg btn-primary">Подать заявку на услугу</a>
                    <a href="<?= Url::to(['category/index#category']) ?>" class="btn btn-lg btn-outline-primary">
                        Выбор услуги
                    </a>
                </div>
            </div>
        </div>
        <?= app\components\backgroundWidget::widget() ?>
    </div>

    <div class="container">
        <h1>Доступные купоны на скидку</h1>
        <div class="coupons_items row">
            <?php if (!empty($coupons)) { ?>
                <?php foreach ($coupons as $coupon) { ?>
                    <?= $this->render('/layouts/coupon_item', [
                        'coupon' => $coupon,
                    ]) ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

<?= app\widgets\OrderWidget::widget() ?>