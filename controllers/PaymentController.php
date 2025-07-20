<?php

namespace app\controllers;

use yii\web\Controller;

class PaymentController extends Controller
{
    /**
     * Success URL - страница успешной оплаты для пользователя
     * https://www.beautyms.ru/success
     */
    public function actionSuccess()
    {
        $this->layout = 'no-footer'; // Layout без футера
        return $this->render('success');
    }

    /**
     * Fail URL - страница неуспешной оплаты для пользователя
     * https://www.beautyms.ru/fail
     */
    public function actionFail()
    {
        $this->layout = 'main';
        return $this->render('fail');
    }
}