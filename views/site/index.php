<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;

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
    <?= app\components\backgroundWidget::widget() ?>
</div>

<div id="category" class="container">
    <h1>Категории услуг</h1>
    <div class="category_items row">
        <?php foreach ($category as $category_item) : ?>
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
        <?php endforeach ?>
    </div>
    <h1>Доступные купоны на скидку</h1>
    <div class="coupons_items row">
        <?php foreach ($coupons as $coupon) : ?>
            <?= $this->render('/layouts/coupon_item', [
                'coupon' => $coupon,
            ]) ?>
        <?php endforeach ?>
    </div>
</div>
<div class="reviews">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="review_main">
                    <span class="review_info">ОТЗЫВЫ</span>
                    <h4>Купонами<br />уже<br />воспользовались </h4>
                    <img src="/images/reviews/photo_reviews.png" />
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="review_item row">
                    <div class="col-md-4 col-12">
                        <img src="/images/reviews/photo_review_1.png" />
                        <div class="review_info">
                            <h4>Катя</h4>
                            <span>23 года</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-12">
                        <p>Нужно было срочно сделать маникюр и педикюр перед отпуском, оставался буквально один день и
                            времени было в обрез! Отправила заявку, менеджер позвонила буквально через 10 минут,
                            оперативно согласовали приезд мастера. В итоге я успела и вещи собрать и ноготочки привести
                            в порядок. Мастер, кстати, отличный!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="review_item row">
                    <div class="col-md-4 col-12">
                        <img src="/images/reviews/photo_review_2.png" />
                        <div class="review_info">
                            <h4>Алина</h4>
                            <span>25 года</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-12">
                        <p>У меня сильно отросли волосы, а из-за маленького ребенка выбраться в парикмахерскую не могла
                            уже который месяц. Хотелось поухаживать за собой, ведь и в декрете хочется оставаться
                            девушкой! Приехала прекрасная девушка-стилист — привела мои волосы в порядок, помогла снова
                            почувствовать себя красивой и привлекательной. Обязательно обращусь снова!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="review_item row">
                    <div class="col-md-4 col-12">
                        <img src="/images/reviews/photo_review_3.png" />
                        <div class="review_info">
                            <h4>Елена В.</h4>
                            <span>30 года</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-12">
                        <p>У меня "проблемные" ножки. Заказала купон, выбрали мастера. Медицинский и косметический
                            педикюр мне очень понравились! Смотрю на ножки и понимаю, что они не так ужасны как я о них
                            думаю. Еще и массаж ножек сделали в подарок. Спасибо! Все супер!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="advantages">
    <div class="row">
        <div class="col-md-6 col-12">
        </div>
        <div class="col-md-6 col-12">
            <h2>ПРЕИМУЩЕСТВА!</h2>
            <h1>Вы получаете</h1>

            <div class="item">
                <img src="/images/icons/icon_check.svg" />
                <div>
                    <h4>Экономию времени</h4>
                    <p>Не нужно ехать в салон красоты</p>
                </div>
            </div>
            <div class="item">
                <img src="/images/icons/icon_check.svg" />
                <div>
                    <h4>Гарантию 100%</h4>
                    <p>На качество услуг</p>
                </div>
            </div>
            <div class="item">
                <img src="/images/icons/icon_check.svg" />
                <div>
                    <h4>Оригинальные и качественные материалы</h4>
                    <p>Mavala, O.P.I., P.Shine, Beautix и др.</p>
                </div>
            </div>
            <div class="item">
                <img src="/images/icons/icon_check.svg" />
                <div>
                    <h4>Стерильные инструменты </h4>
                    <p>Мастера привозят все необходимые инструменты и материалы с собой</p>
                </div>
            </div>
            <div class="item">
                <img src="/images/icons/icon_check.svg" />
                <div>
                    <h4>Профессиональные подход</h4>
                    <p>Все наши мастера проверены и имеют сертификаты, оказывают профессиональные услуги на дому</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-12">
            <div class="sale_block">
                <img src="/images/sale.png" />
                <div>
                    <h4>Покупая купон на скидку, вы делаете правильный выбор в пользу себя</h4>
                    <p>— вы не только ухаживаете за собой,<br />но и экономите время и деньги! </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= app\widgets\OrderWidget::widget() ?>

<div class="map">
    <div class="row">
        <div class="col-md-6 col-12 map-left-block">
            <div class="map-block">
                <h2>География работы<br />мастеров</h2>
                <div class="map-list-item">
                    <img src="/images/icons/icon_map.svg" />
                    <div>
                        <h4>Москва</h4>
                        <p>Вы можете воспользоваться услугой<br />в любом районе Москвы</p>
                    </div>
                </div>
                <div class="map-list-item">
                    <img src="/images/icons/icon_work.svg" />
                    <div>
                        <h4>Режим работы</h4>
                        <p>с 8:00 до 22:00 без выходных</p>
                    </div>
                </div>
                <div class="map-list-item">
                    <img src="/images/icons/icon_comment.svg" />
                    <div>
                        <h4>Согласование приезда</h4>
                        <p>С вами свяжется менеджер для уточнения адреса и времени приезда мастера. В случае
                            необходимости приезд мастера можно согласовать дополнительно.</p>
                    </div>
                </div>
                <br />
                <?php if (Yii::$app->user->isGuest) : ?>
                    <?= Html::a('Хотите стать мастером?', ["site/register-master"], ["class" => "btn-master"]) ?>
                <?php endif; ?>
                <br />
                <br />
            </div>
        </div>
        <div class="col-md-6 col-12">
            <script>
                ymaps.ready(init);

                function init() {
                    var myMap = new ymaps.Map("main-map", {
                        center: [55.755819, 37.617644],
                        zoom: 11
                    }, {
                        searchControlProvider: 'yandex#search'
                    });
                    <?php foreach ($masters as $master) : ?>
                        myMap.geoObjects.add(new ymaps.Placemark([<?= $master->work_lat ?>, <?= $master->work_lon ?>], {
                            balloonContentHeader: '<?= $master->middlename." ".mb_substr($master->firstname, 0, 1); ?>.',
                        }, {
                            iconLayout: 'default#image',
                            iconImageHref: '<?= Url::to(['images/map_icons/'.rand(1, 7).'.png']) ?>',
                            iconImageSize: [40, 40],
                            iconImageOffset: [0, 0]
                        }
                        ));
                    <?php endforeach ?>
                }
            </script>
            <div id="main-map"></div>
        </div>
    </div>
</div>