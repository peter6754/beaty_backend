<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use yii\web\JsExpression;
use yii\web\View;

$format = <<<SCRIPT
    function format(state) {
        return state.text;
    }
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");
$this->registerJs($format, View::POS_HEAD);

?>

<script>
    window.category_id = 1;
    window.timeStamp = 0;
</script>

<div id="order" class="order">
    <div class="container">
        <?php $form = ActiveForm::begin([
            'id' => 'order-form',
            'enableAjaxValidation' => true,
            'validationUrl' => ['order/validation'],
            'action' => ['order/create']
        ]); ?>

        <div class="row">
            <div style="position: relative;" class="col-md-6 col-12">
                <h2>Подать заявку на услугу</h2>
                <p>С вами свяжется менеджер для уточнения адреса и времени приезда мастера</p>
                <img class="click" src="<?= Url::to(['../images/click.png']) ?>" />

                <h4>Ваши контактные данные</h4>
                <?= $form->field($model, 'name')->textInput(['placeholder' => $model->getAttributeLabel('name')])->label(false) ?>
                <?= $form->field($model, 'phone')->textInput(['class' => 'form-control phone_mask', 'placeholder' => $model->getAttributeLabel('phone')])->label(false) ?>

                <h4>Дата и время</h4>
                <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                    'options' => [
                        'placeholder' => "Выберите дату",
                    ],
                    'readonly' => true,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'todayHighlight' => true,
                        'startDate' => date('d.m.Y'),
                        'format' => 'dd.mm.yyyy'
                    ],
                    'pluginEvents' => [
                        "changeDate" => "function(e) { window.timeStamp = e.timeStamp }",
                    ]
                ])->label(false) ?>

                <?= $form->field($model, 'time')->widget(Select2::classname(), [
                    'pluginOptions' => [
                        'placeholder' => "Выберите время",
                        'ajax' => [
                            'url' => new JsExpression('function(params) { return "/api/order/time?date=" + window.timeStamp; }'),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                    ],
                ])->label(false) ?>

                <h4>адрес</h4>
                <div style="position: relative">
                    <?= $form->field($model, 'city')->textInput(['placeholder' => $model->getAttributeLabel('city')])->label(false) ?>
                    <button class="btn btn-light btn-locate" type="button"><i class="fa fa-map-marker"></i></button>
                </div>

                <?= $form->field($model, 'street')->textInput(['placeholder' => $model->getAttributeLabel('street')])->label(false) ?>

                <div class="row">
                    <div class="col-md-4 col-6">
                        <?= $form->field($model, 'house')->textInput(['placeholder' => $model->getAttributeLabel('house')])->label(false) ?>
                    </div>
                    <div class="col-md-4 col-6">
                        <?= $form->field($model, 'apartment')->textInput(['placeholder' => $model->getAttributeLabel('apartment')])->label(false) ?>
                    </div>
                    <div class="col-md-4 col-6">
                        <?= $form->field($model, 'entrance')->textInput(['placeholder' => $model->getAttributeLabel('entrance')])->label(false) ?>
                    </div>
                    <div class="col-md-4 col-6">
                        <?= $form->field($model, 'floor')->textInput(['placeholder' => $model->getAttributeLabel('floor')])->label(false) ?>
                    </div>
                    <div class="col-md-4 col-6">
                        <?= $form->field($model, 'intercom')->textInput(['placeholder' => $model->getAttributeLabel('intercom')])->label(false) ?>
                    </div>
                </div>
                <?= $form->field($model, 'lat')->hiddenInput()->label(false) ?>

                <?= $form->field($model, 'lon')->hiddenInput()->label(false) ?>
            </div>
            <div class="col-md-6 col-12">

                <h4>комментарии</h4>

                <?= $form->field($model, 'comment')->textarea(['placeholder' => "Укажите дополнительную информацию", 'rows' => '6'])->label(false) ?>

                <div class="order-category">
                    <?= Select2::widget([
                        'name' => 'category',
                        'value' => ! empty($category) ? $category[0]->id : null,
                        'data' => $category_data,
                        'pluginOptions' => [
                            'escapeMarkup' => $escape,
                            'allowClear' => false
                        ],
                        'pluginEvents' => [
                            "select2:selecting" => "function(e) { 
                                    window.category_id = e.params.args.data.id;
                                }",
                        ]
                    ])
                        ?>
                </div>
                <?= $form->field($model, 'product_id')->widget(Select2::classname(), [
                    'pluginOptions' => [
                        'placeholder' => "Выберите услугу",
                        'ajax' => [
                            'url' => new JsExpression('function(params) { return "/api/order/list?id=" + window.category_id; }'),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(lista_art) { return lista_art.html; }'),
                        'templateSelection' => new JsExpression('function (lista_art) { return lista_art.text }')
                    ],
                    'pluginEvents' => [
                        "select2:selecting" => "function(e) { 
                            loadCoupon(e.params.args.data.id);
                        }",
                    ]


                ])->label(false) ?>

                <?= $form->field($model, 'order_coupon_id')->hiddenInput()->label(false); ?>
                <div class="coupon_block">

                </div>
            </div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('Подать заявку', ['id' => 'order_button', 'class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>