<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=<?= Yii::$app->params['ymap_api_key'] ?>"
        type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/css/suggestions.min.css" rel="stylesheet" />
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <?php if (Yii::$app->user->isGuest) : ?>

        <?= app\widgets\LoginWidget::widget() ?>
        <?= app\widgets\ForgotWidget::widget() ?>
        <?= app\widgets\RegisterWidget::widget() ?>

    <?php endif ?>

    <?= app\widgets\CouponWidget::widget() ?>

    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="/"><img src="<?= Url::to(['/images/logo.svg']) ?>" /></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Переключатель навигации">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Категории</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?= app\components\categoryWidget::widget() ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('О нас', ["site/about"], ["class" => "nav-link"]) ?>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('Оплата', ["site/payment"], ["class" => "nav-link"]) ?>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('Контакты', ["site/contact"], ["class" => "nav-link"]) ?>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('Новости', ["news/index"], ["class" => "nav-link"]) ?>
                        </li>
                        <?php if (Yii::$app->user->isGuest) : ?>
                            <li class="nav-item">
                                <?= Html::a('Стать мастером', ["site/register-master"], ["class" => "nav-link"]) ?>
                            </li>
                        <?php endif; ?>
                        <!--<li class="nav-item">
                         <a class="nav-link btn btn-light" href="#"><img src="<?php //=Url::to(['/images/icons/icon_cart.svg']) ?>" /> Корзина</a>
                     </li>-->
                        <?php if (Yii::$app->user->isGuest) : ?>
                            <li class="nav-item">
                                <?= Html::a('Регистрация', '#', ['data-bs-toggle' => 'modal', 'data-bs-target' => '#register-modal', "class" => "nav-link btn btn-primary"]) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('Войти', '#', ['data-bs-toggle' => 'modal', 'data-bs-target' => '#login-modal', "class" => "nav-link"]) ?>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <?= Html::a('Личный кабинет', ["profile/index"], ["class" => "nav-link btn btn-primary"]) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('Выход', ["/site/logout"], ["class" => "nav-link"]) ?>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
    </div>

    <?= $content ?>

    <footer class="footer mt-auto py-3 text-muted">

        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="/"><img src="<?= Url::to(['/images/logo.svg']) ?>" /></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Переключатель навигации">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Категории</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                                <?= app\components\categoryWidget::widget() ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('О нас', ["site/about"], ["class" => "nav-link"]) ?>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('Оплата', ["site/payment"], ["class" => "nav-link"]) ?>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('Контакты', ["site/contact"], ["class" => "nav-link"]) ?>
                        </li>
                        <li class="nav-item">
                            <?= Html::a('Новости', ["news/index"], ["class" => "nav-link"]) ?>
                        </li>
                        <!--<li class="nav-item">
                        <a class="nav-link btn btn-light" href="#"><img src="<?php //=Url::to(['/images/icons/icon_cart.svg']) ?>" /> Корзина</a>
                    </li>-->
                        <?php if (Yii::$app->user->isGuest) : ?>
                            <li class="nav-item">
                                <?= Html::a('Регистрация', '#', ['data-bs-toggle' => 'modal', 'data-bs-target' => '#register-modal', "class" => "nav-link btn btn-primary"]) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('Войти', '#', ['data-bs-toggle' => 'modal', 'data-bs-target' => '#login-modal', "class" => "nav-link"]) ?>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <?= Html::a('Личный кабинет', ["profile/index"], ["class" => "nav-link btn btn-primary"]) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('Выход', ["/site/logout"], ["class" => "nav-link"]) ?>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <a href="<?= Url::to(['/Публичная оферта.pdf']) ?>" class="politics">Публичная оферта</a>
            <br />
            <a href="<?= Url::to(['/Политика конфиденциальности.pdf']) ?>" class="politics">Политика
                конфиденциальности</a>
        </div>
    </footer>

    <?php $this->endBody() ?>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/js/jquery.suggestions.min.js"></script>
</body>

</html>
<?php $this->endPage() ?>