<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>

<div class="col-md-3 col-12">
    <a class="coupon_link" href="#">
        <div class="coupon_item" style="background-color: <?= $coupon->category->color ?>">
            <img src="<?= $coupon->getImageUrl("250x250") ?>" />
            <div class="coupon_description">
                <div class="coupon_left">
                    <span class="coupon_type">КУПОН</span>
                    <h4><?= $coupon->name ?></h4>
                </div>
                <div class="coupon_amount">-<?= $coupon->amount ?>р.</div>
                <span class="coupon_name"><?= $coupon->description ?></span>
                <div class="coupon_bottom">
                    <span class="coupon_price"><?= $coupon->price ?>р.</span>
                    <div class="buttons">
                        <!--
                        <a href="#" class="btn btn-sm btn-primary">В корзину</a>
                        <a href="#" class="btn btn-sm btn-outline-primary">Купить сразу</a>
                        -->
                        <?php if (Yii::$app->user->isGuest) : ?>
                            <?= Html::submitButton('Купить сразу', ["id" => "buy_coupon", "data-coupon_id" => $coupon->id, "class" => "btn btn-sm btn-outline-primary"]) ?>
                        <?php else : ?>
                            <a href="<?= Url::to(['coupon/order', 'id' => $coupon->id]) ?>"
                                class="btn btn-sm btn-primary">Купить сразу</a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>