<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AdminAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/css/suggestions.min.css" rel="stylesheet" />
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'encodeLabels' => false,
        'items' => [
            [
                'visible' => Yii::$app->user->can('admin'),
                'label' => 'Пользователи',
                'items' => [
                    '<div  class="dropdown-header">Пользователи</div >',
                    ['visible' => !Yii::$app->user->isGuest, 'label' => 'Клиенты', 'url' => ['/admin/user/index']],
                    '<div  class="dropdown-header">Управление доступом</div >',
                    ['visible' => !Yii::$app->user->isGuest, 'label' => 'Маршруты', 'url' => ['/rbac/route']],
                    ['visible' => !Yii::$app->user->isGuest, 'label' => 'Доступы', 'url' => ['/rbac/permission']],
                    ['visible' => !Yii::$app->user->isGuest, 'label' => 'Роли', 'url' => ['/rbac/role']],
                ],
            ],
            ['visible' => Yii::$app->user->can('admin'), 'label' => 'Категории', 'url' => ['/admin/category/index']],
            ['visible' => Yii::$app->user->can('admin'), 'label' => 'Купоны', 'url' => ['/admin/coupon/index']],
            ['visible' => Yii::$app->user->can('admin'), 'label' => 'Услуги', 'url' => ['/admin/product/index']],
            ['visible' => Yii::$app->user->can('admin'), 'label' => 'Заказы', 'url' => ['/admin/order-application/index']],
            ['visible' => Yii::$app->user->can('admin'), 'label' => 'Мастера', 'url' => ['/admin/master/index']],
            ['visible' => Yii::$app->user->isGuest, 'label' => 'Авторизоваться', 'url' => ['/site/login']],
            ['visible' => !Yii::$app->user->isGuest, 'label' => 'Выход (' . Yii::$app->user->identity->name . ')', 'url' => ['/site/logout']]
        ],
    ]);
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">&copy; My Company <?= date('Y') ?></p>
        <p class="float-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/js/jquery.suggestions.min.js"></script>
</body>
</html>
<?php $this->endPage() ?>
