<?php

use yii\helpers\Url;

$this->title = 'Beautyms';
?>
<div class="banner">
    <div class="container">
        <div class="block-title">
            <h2>УХОД, КОТОРОГО ВЫ ДОСТОЙНЫ!</h2>
            <h1>Бьюти услуги<br />с выездом к вам!</h1>
            <h3>Купите купон на скидку от 99 рублей<br />и сэкономьте <span>до 1000 рублей!</span></h3>
            <div class="buttons">
                <a href="#order" class="btn btn-lg btn-primary">Подать заявку на услугу</a>
                <a href="#category" class="btn btn-lg btn-outline-primary">Выбор услуги</a>
            </div>
        </div>
    </div>
    <?= app\components\backgroundWidget::widget(["type" => "category"]) ?>
</div>

<div id="category" class="container">
    <h1>Категории услуг</h1>
    <div class="category_items row">
        <?php if (! empty($categories)) { ?>
            <?php foreach ($categories as $category_item) { ?>
                <div class="col-md-3 col-6">
                    <a class="category_link" href="<?= Url::to(['category/view', 'id' => $category_item->id]) ?>"
                        data-category="<?= $category_item->id ?>">
                        <div class="category_item" style="background-color: <?= $category_item->color ?>">
                            <div class="category_image"
                                style="background-image: url(<?= $category_item->getImageUrl("x142") ?>)">
                                <h4><?= $category_item->name ?></h4>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>

<?= app\widgets\OrderWidget::widget() ?>