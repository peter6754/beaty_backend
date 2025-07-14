<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\file\FileInput;
use yii\helpers\Url;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="master-form">
        <h2 class="text-center"><?= $this->title ?></h2>
        <h5 class="text-center">Введите свои контакты, чтобы начать брать заказы</h5>

        <?php if (! empty($masterForm)) : ?>
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6 col-12">

                    <h4>ФИО</h4>

                    <?= $form->field($masterForm, 'firstname')->textInput(['placeholder' => $masterForm->getAttributeLabel('firstname')])->label(false) ?>

                    <?= $form->field($masterForm, 'middlename')->textInput(['placeholder' => $masterForm->getAttributeLabel('middlename')])->label(false) ?>

                    <?= $form->field($masterForm, 'lastname')->textInput(['placeholder' => $masterForm->getAttributeLabel('lastname')])->label(false) ?>

                    <h4>Электронная почта</h4>

                    <?= $form->field($masterForm, 'email')->textInput(['placeholder' => $masterForm->getAttributeLabel('email')])->label(false) ?>

                    <h4>пол</h4>

                    <?= $form->field($masterForm, 'gender')->dropDownList([
                        "0" => "Мужской",
                        "1" => "Женский",
                    ])->label(false) ?>

                </div>

                <div class="col-md-6 col-12">

                    <h4>дата рождения</h4>

                    <?= $form->field($masterForm, 'birthday')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Выберите дату рождения'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ])->label(false) ?>

                    <h4>Номер телефона</h4>

                    <?= $form->field($masterForm, 'phone')->textInput(['class' => 'form-control phone_mask', 'placeholder' => $masterForm->getAttributeLabel('phone')])->label(false) ?>

                    <h4>ПРИДУМАЙТЕ ПАРОЛЬ</h4>

                    <?= $form->field($masterForm, 'password')->passwordInput(['placeholder' => $masterForm->getAttributeLabel('password')])->label(false) ?>

                    <?= $form->field($masterForm, 'password_repeat')->passwordInput(['placeholder' => $masterForm->getAttributeLabel('password_repeat')])->label(false) ?>

                </div>
            </div>

            <div class="form-group text-center">
                <br />
                <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-lg btn-primary']) ?>

                <br />
                <br />
                <?php if (YII_ENV_DEV) : ?>
                    <p><strong>ТЕСТОВЫЙ РЕЖИМ:</strong> Оплата пропущена для разработки.</p>
                <?php else : ?>
                    <p>Для отправки заявки на регистрацию, Вам необходимо оплатить регистрационный сбор в размере 199
                        руб.<br />Оплачивая Вы соглашаетесь с офертой.</p>
                <?php endif; ?>

            </div>

            <?php ActiveForm::end(); ?>
        <?php endif ?>

        <?php if (! empty($masterProceedForm)) : ?>
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6 col-12">

                    <?= $form->field($masterProceedForm, 'hash')->hiddenInput()->label(false) ?>

                    <h4>УСЛУГИ</h4>

                    <?= $form->field($masterProceedForm, 'products')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'data' => $products,
                        'model' => $masterProceedForm,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => true,
                        ],
                    ])->label(false) ?>

                    <h4>РАДИУС ПОИСКА ЗАКАЗОВ</h4>

                    <?= $form->field($masterProceedForm, 'search_radius')->textInput(["min" => "1", 'type' => 'number', 'placeholder' => 'Укажите значение, км'])->label(false) ?>

                    <h4>относительно чего ищем заказы</h4>

                    <?= $form->field($masterProceedForm, 'work_city')->textInput(['placeholder' => $masterProceedForm->getAttributeLabel('work_city')])->label(false) ?>

                    <?= $form->field($masterProceedForm, 'work_street')->textInput(['placeholder' => $masterProceedForm->getAttributeLabel('work_street')])->label(false) ?>

                    <?= $form->field($masterProceedForm, 'work_house')->textInput(['placeholder' => $masterProceedForm->getAttributeLabel('work_house')])->label(false) ?>

                    <?= $form->field($masterProceedForm, 'work_lat')->hiddenInput()->label(false) ?>

                    <?= $form->field($masterProceedForm, 'work_lon')->hiddenInput()->label(false) ?>

                    <h4>фото ПАСПОРТА</h4>

                    <?= FileInput::widget([
                        'id' => 'passport',
                        'language' => 'ru',
                        'name' => 'file',
                        'options' => [
                            'multiple' => true,
                        ],
                        'pluginEvents' => [
                            "filebatchselected" => 'function() { $("#passport").fileinput("upload"); }',
                        ],
                        'pluginOptions' => [
                            'showCaption' => false,
                            'showUpload' => false,
                            'showRemove' => false,
                            //  'initialPreview'=> $initialPreview,
                            'initialPreviewAsData' => true,
                            // 'initialPreviewConfig' => $initialPreviewConfig,
                            'overwriteInitial' => false,
                            'deleteUrl' => Url::to(['/api/files/delete']),
                            'deleteExtraData' => [
                                'hash' => $masterPreceedForm->hash,
                                'type' => 1,
                            ],
                            'uploadUrl' => Url::to(['/api/files/upload']),
                            'uploadAsync' => true,
                            'uploadExtraData' => [
                                'hash' => $masterPreceedForm->hash,
                                'type' => 1,
                            ],
                            'allowedFileExtensions' => ['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt', 'pptx', 'ppt', 'gif'],
                            'maxFileCount' => 10,
                            'maxFileSize' => 104857600,
                        ]
                    ]); ?>
                    <br />

                    <h4>желаемый пол клиента</h4>

                    <?= $form->field($masterProceedForm, 'client_gender')->dropDownList([
                        "0" => "Мужской",
                        "1" => "Женский",
                        "2" => "Любой"
                    ])->label(false) ?>
                </div>

                <div class="col-md-6 col-12">

                    <h4>АДРЕС ВАШЕГО ПРОЖИВАНИЯ</h4>

                    <?= $form->field($masterProceedForm, 'live_city')->textInput(['placeholder' => $masterProceedForm->getAttributeLabel('live_city')])->label(false) ?>

                    <?= $form->field($masterProceedForm, 'live_street')->textInput(['placeholder' => $masterProceedForm->getAttributeLabel('live_street')])->label(false) ?>

                    <?= $form->field($masterProceedForm, 'live_house')->textInput(['placeholder' => $masterProceedForm->getAttributeLabel('live_house')])->label(false) ?>

                    <?= $form->field($masterProceedForm, 'live_apartment')->textInput(['placeholder' => $masterProceedForm->getAttributeLabel('live_apartment')])->label(false) ?>

                    <?= $form->field($masterProceedForm, 'live_lat')->hiddenInput()->label(false) ?>

                    <?= $form->field($masterProceedForm, 'live_lon')->hiddenInput()->label(false) ?>

                    <h4>фото работ</h4>

                    <?= FileInput::widget([
                        'id' => 'works',
                        'language' => 'ru',
                        'name' => 'file',
                        'options' => [
                            'multiple' => true,
                        ],
                        'pluginEvents' => [
                            "filebatchselected" => 'function() { $("#works").fileinput("upload"); }',
                        ],
                        'pluginOptions' => [
                            'showCaption' => false,
                            'showUpload' => false,
                            'showRemove' => false,
                            //  'initialPreview'=> $initialPreview,
                            'initialPreviewAsData' => true,
                            // 'initialPreviewConfig' => $initialPreviewConfig,
                            'overwriteInitial' => false,
                            'deleteUrl' => Url::to(['/api/files/delete']),
                            'deleteExtraData' => [
                                'hash' => $masterProceedForm->hash,
                                'type' => 2,
                            ],
                            'uploadUrl' => Url::to(['/api/files/upload']),
                            'uploadAsync' => true,
                            'uploadExtraData' => [
                                'hash' => $masterProceedForm->hash,
                                'type' => 2,
                            ],
                            'allowedFileExtensions' => ['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt', 'pptx', 'ppt', 'gif'],
                            'maxFileCount' => 10,
                            'maxFileSize' => 104857600,
                        ]
                    ]); ?>

                    <h4>ЛИЦЕНЗИИ/СЕРТИФИКАТЫ</h4>

                    <?= FileInput::widget([
                        'id' => 'licenses',
                        'language' => 'ru',
                        'name' => 'file',
                        'options' => [
                            'multiple' => true,
                        ],
                        'pluginEvents' => [
                            "filebatchselected" => 'function() { $("#licenses").fileinput("upload"); }',
                        ],
                        'pluginOptions' => [
                            'showCaption' => false,
                            'showUpload' => false,
                            'showRemove' => false,
                            //  'initialPreview'=> $initialPreview,
                            'initialPreviewAsData' => true,
                            // 'initialPreviewConfig' => $initialPreviewConfig,
                            'overwriteInitial' => false,
                            'deleteUrl' => Url::to(['/api/files/delete']),
                            'deleteExtraData' => [
                                'hash' => $masterProceedForm->hash,
                                'type' => 3,
                            ],
                            'uploadUrl' => Url::to(['/api/files/upload']),
                            'uploadAsync' => true,
                            'uploadExtraData' => [
                                'hash' => $masterProceedForm->hash,
                                'type' => 3,
                            ],
                            'allowedFileExtensions' => ['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt', 'pptx', 'ppt', 'gif'],
                            'maxFileCount' => 10,
                            'maxFileSize' => 104857600,
                        ]
                    ]); ?>

                </div>
            </div>

            <div class="form-group text-center">
                <br />
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-lg btn-primary']) ?>

            </div>

            <?php ActiveForm::end(); ?>
        <?php endif ?>
    </div>
</div>