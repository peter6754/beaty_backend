<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Beautyms';
?>

<div class="profile">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="block-title">
                    <h2>личный кабинет</h2>
                    <h1>Добро пожаловать,<br /><?= $user->name ?></h1>
                    <?php if (Yii::$app->user->can('master')) : ?>
                        <h3>Ваш баланс: 0 ₽</h3>
                    <?php else : ?>
                        <h3>Здесь вы можете посмотреть историю заказов или изменить контактные данные </h3>
                    <?php endif ?>
                    <div class="buttons">
                        <a href="#order" class="btn btn-lg btn-primary">Подать заявку на услугу</a>
                        <a href="<?= Url::to(['category/index']) ?>" class="btn btn-lg btn-outline-primary">Выбор
                            услуги</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-edit">
                    <h3>Основная информация</h3>

                    <?php if (Yii::$app->session->hasFlash('error')) : ?>
                        <div class="alert alert-danger">
                            <?= Yii::$app->session->getFlash('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($user, 'name')->textInput(); ?>

                    <?= $form->field($user, 'email')->textInput(); ?>

                    <div class="form-group">
                        <div class="text-center">

                            <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary auth-button', 'name' => 'register-button']); ?>

                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php if (Yii::$app->user->can('master')) : ?>
        <div class="profile-edit">
            <h3>Мои заказы</h3>
            <br />
            <br />
            <br />
        </div>
    <?php endif ?>

    <div class="profile-edit">
        <h3>История заказов</h3>
        <br />
        <br />
        <br />
    </div>

</div>

<?= app\widgets\OrderWidget::widget() ?>