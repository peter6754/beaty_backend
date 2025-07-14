<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use app\models\Coupon;
use app\models\CouponForm;
use app\models\OrderCoupon;
use app\models\User;
use YooKassa\Client;
use yii\web\HttpException;

class CouponController extends Controller
{
    public function beforeAction($action)
    {
        if ($action->id === 'register') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionOrder($id)
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(401, 'Необходима авторизация');
        }

        $coupon = Coupon::findOne($id);
        if (! $coupon) {
            throw new NotFoundHttpException('Купон не найден');
        }

        $order = new OrderCoupon([
            "user_id" => Yii::$app->user->identity->id,
            "coupon_id" => $coupon->id,
            "price" => $coupon->price,
            "date" => time()
        ]);
        if (! $order->save()) {
            throw new HttpException(500, 'Ошибка при создании заказа');
        }

        try {
            // В режиме разработки просто симулируем успешный платеж
            if (YII_ENV === 'dev') {
                $order->order_id = 'test_'.time();
                $order->save();

                Yii::$app->session->setFlash('success', 'Платеж обработан (тестовый режим). Купон успешно приобретен!');
                return $this->redirect(Yii::$app->homeUrl);
            }

            $client = new Client();
            $client->setAuth(Yii::$app->params['yoomoney_shopid'], Yii::$app->params['yoomoney_secret']);
            $response = $client->createPayment(
                array(
                    'amount' => array(
                        'value' => $coupon->price,
                        'currency' => 'RUB',
                    ),
                    'confirmation' => array(
                        'type' => 'redirect',
                        'locale' => 'ru_RU',
                        'return_url' => 'https://beautyms.ru/',
                    ),
                    'capture' => true,
                    'description' => $coupon->name
                ),
                "C".$order->id
            );

            $order->order_id = $response->getId();
            $order->save();

            return $this->redirect($response->getConfirmation()->getConfirmationUrl());
        } catch (\Exception $e) {
            Yii::error('Ошибка создания платежа: '.$e->getMessage());
            throw new HttpException(500, 'Ошибка при создании платежа');
        }
    }

    public function actionRegister()
    {
        if (Yii::$app->request->isAjax) {
            // Additional security check since CSRF is disabled
            if (! Yii::$app->request->headers->get('X-Requested-With')) {
                throw new HttpException(400, 'Invalid request');
            }

            $model = new CouponForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $user = Yii::$app->user->isGuest ? User::findOne(["phone" => preg_replace('/[^0-9]/', '', $model->phone)]) : Yii::$app->user->identity;
                if (empty($user)) {

                    $user = new User([
                        "name" => $model->name,
                        "phone" => $model->phone,
                        "password" => Yii::$app->getSecurity()->generatePasswordHash("00000000"),
                        "token" => Yii::$app->getSecurity()->generateRandomString()
                    ]);
                    $user->save();

                    Yii::$app->user->login($user, 3600 * 24 * 30);
                }

                $coupon = Coupon::findOne($model->coupon_id);
                if (! $coupon) {
                    throw new NotFoundHttpException('Купон не найден');
                }

                $order = new OrderCoupon([
                    "user_id" => $user->id,
                    "coupon_id" => $coupon->id,
                    "price" => $coupon->price,
                    "date" => time()
                ]);
                if (! $order->save()) {
                    throw new HttpException(500, 'Ошибка при создании заказа');
                }

                try {
                    // В режиме разработки просто симулируем успешный платеж
                    if (YII_ENV === 'dev') {
                        $order->order_id = 'test_'.time();
                        $order->save();

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'status' => 'success',
                            'message' => 'Платеж обработан (тестовый режим)',
                            'redirect' => Yii::$app->homeUrl
                        ];
                    }

                    $client = new Client();
                    $client->setAuth(Yii::$app->params['yoomoney_shopid'], Yii::$app->params['yoomoney_secret']);
                    $response = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $coupon->price,
                                'currency' => 'RUB',
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'locale' => 'ru_RU',
                                'return_url' => 'https://beautyms.ru/',
                            ),
                            'capture' => true,
                            'description' => $coupon->name
                        ),
                        "C".$order->id
                    );

                    $order->order_id = $response->getId();
                    $order->save();

                    return $this->redirect($response->getConfirmation()->getConfirmationUrl());
                } catch (\Exception $e) {
                    Yii::error('Ошибка создания платежа: '.$e->getMessage());
                    throw new HttpException(500, 'Ошибка при создании платежа');
                }
            } else {
                Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            throw new HttpException(404, 'Страница не найдена');
        }
    }
}